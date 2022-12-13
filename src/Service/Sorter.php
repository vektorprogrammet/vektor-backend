<?php

namespace App\Service;

use App\Entity\Receipt;
use App\Entity\TeamMembershipInterface;
use App\Entity\User;

class Sorter
{
    /**
     * @return int
     */
    protected function userWithNewestReceipt(User $user1, User $user2)
    {
        $user1Receipts = $user1->getReceipts()->toArray();
        $user2Receipts = $user2->getReceipts()->toArray();

        $this->sortReceiptsBySubmitTime($user1Receipts);
        $this->sortReceiptsBySubmitTime($user2Receipts);

        if (empty($user1Receipts) && empty($user2Receipts)) {
            return 0;
        }

        if (empty($user1Receipts)) {
            return 1;
        }

        if (empty($user2Receipts)) {
            return -1;
        }

        return $this->newestReceipt($user1Receipts[0], $user2Receipts[0]);
    }

    public function newestReceipt(Receipt $receipt1, Receipt $receipt2): int
    {
        if ($receipt1->getSubmitDate() === $receipt2->getSubmitDate()) {
            return 0;
        }

        return ($receipt1->getSubmitDate() > $receipt2->getSubmitDate()) ? -1 : 1;
    }

    /**
     * @param User[] $users
     *
     * @return bool success
     */
    public function sortUsersByReceiptSubmitTime(&$users): bool
    {
        return usort($users, [$this, 'userWithNewestReceipt']);
    }

    /**
     * @param User[] $users
     */
    public function sortUsersByReceiptStatus(&$users)
    {
        $usersWithPendingReceipts = [];
        $usersWithoutPendingReceipts = [];
        foreach ($users as $user) {
            if ($user->hasPendingReceipts()) {
                $usersWithPendingReceipts[] = $user;
            } else {
                $usersWithoutPendingReceipts[] = $user;
            }
        }

        $users = array_merge($usersWithPendingReceipts, $usersWithoutPendingReceipts);
    }

    /**
     * @param Receipt[] $receipts
     *
     * @return bool success
     */
    public function sortReceiptsBySubmitTime(&$receipts)
    {
        return usort($receipts, [$this, 'newestReceipt']);
    }

    /**
     * @param Receipt[] $receipts
     */
    public function sortReceiptsByStatus(&$receipts)
    {
        $pendingReceipts = [];
        $nonPendingReceipts = [];
        foreach ($receipts as $receipt) {
            if (Receipt::STATUS_PENDING === $receipt->getStatus()) {
                $pendingReceipts[] = $receipt;
            } else {
                $nonPendingReceipts[] = $receipt;
            }
        }
        $receiptElements = array_merge($pendingReceipts, $nonPendingReceipts);
        $receipts = $receiptElements;
    }

    /**
     * Sorts "leder" og "nestleder" first and the rest in alphabetical order
     * Note! This function does not care WHICH teams the user is active in.
     *
     * @param User[] $users
     */
    public function sortUsersByActivePositions(&$users)
    {
        usort($users, function (User $user1, User $user2) {
            // Get team memberships
            $teamMemberships1 = $user1->getActiveMemberships();
            $teamMemberships2 = $user2->getActiveMemberships();

            // Check if empty or null
            if (null === $teamMemberships2 || empty($teamMemberships2)) {
                if (null === $teamMemberships1 || empty($teamMemberships1)) {
                    return 0; // Both null or empty
                }

                return -1; // If 2 is empty, but not 1:TeamMember 1 comes first
            } elseif (null === $teamMemberships1 || empty($teamMemberships1)) {
                return 1; // If 1 is empty, but not 2: 2 comes first
            }

            // Sort team memberships by position
            $this->sortTeamMembershipsByPosition($teamMemberships1);
            $this->sortTeamMembershipsByPosition($teamMemberships2);

            $cmp = 0;
            for ($i = 0; $i < min(\count($teamMemberships1), \count($teamMemberships2)); ++$i) {
                $cmp = $this->compareTeamMemberships($teamMemberships1[$i], $teamMemberships2[$i]);
                if (0 !== $cmp) {
                    return $cmp; // Non equal positions
                }
            }
            // If tied, prioritize those with the most positions
            if (\count($teamMemberships1) !== \count($teamMemberships2)) {
                return \count($teamMemberships2) - \count($teamMemberships1);
            }

            return $cmp;
        });
    }

    /**
     * Order: "leder" < "nestleder" < "aaa" < "zzz" < "".
     *
     * @param TeamMembershipInterface[] $teamMemberships
     */
    public function sortTeamMembershipsByPosition(&$teamMemberships)
    {
        usort($teamMemberships, [$this, 'compareTeamMemberships']);
    }

    /**
     * @return int
     */
    private function compareTeamMemberships(TeamMembershipInterface $teamMembership1, TeamMembershipInterface $teamMembership2)
    {
        return $this->comparePositions($teamMembership1->getPositionName(), $teamMembership2->getPositionName());
    }

    /**
     * Order: "leder" < "nestleder" < "aaa" < "zzz" < "".
     */
    private function comparePositions(string $position1, string $position2): int
    {
        // Normalize
        $position1 = mb_strtolower($position1);
        $position2 = mb_strtolower($position2);

        // Test equality first to simplify logic below
        if ($position1 === $position2) {
            return 0;
        }

        // Special cases
        if ('leder' === $position1) {
            return -1;
        }
        if ('leder' === $position2) {
            return 1;
        }
        if ('nestleder' === $position1) {
            return -1;
        }
        if ('nestleder' === $position2) {
            return 1;
        }
        if ('' === $position2) {
            return -1;
        }
        if ('' === $position1) {
            return 1;
        }

        // General compare
        return strcmp($position1, $position2);
    }
}
