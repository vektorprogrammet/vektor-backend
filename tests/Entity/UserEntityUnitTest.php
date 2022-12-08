<?php

namespace App\Tests\Entity;

use App\Entity\FieldOfStudy;
use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserEntityUnitTest extends TestCase
{
    // Check whether the setEmail function is working correctly
    public function testSetEmail()
    {
        // new entity
        $user = new User();

        // Use the setEmail method
        $user->setEmail('per@mail.com');

        // Assert the result
        $this->assertEquals('per@mail.com', $user->getEmail());
    }

    // Check whether the setActive function is working correctly
    public function testsetActive()
    {
        // new entity
        $user = new User();

        // Use the setActive method
        $user->setActive(1);

        // Assert the result
        $this->assertEquals(1, $user->isActive());
    }

    // Check whether the setLastName function is working correctly
    public function testSetLastName()
    {
        // new entity
        $user = new User();

        // Use the setLastName method
        $user->setLastName('olsen');

        // Assert the result
        $this->assertEquals('olsen', $user->getLastName());
    }

    // Check whether the setFirstname function is working correctly
    public function testSetFirstname()
    {
        // new entity
        $user = new User();

        // Use the setFirstname method
        $user->setFirstname('olsen');

        // Assert the result
        $this->assertEquals('olsen', $user->getFirstname());
    }

    // Check whether the setGender function is working correctly
    public function testSetGender()
    {
        // new entity
        $user = new User();

        // Use the setGender method
        $user->setGender('0');

        // Assert the result
        $this->assertEquals('0', $user->getGender());
    }

    // Check whether the setPicturePath function is working correctly
    public function testSetPicturePath()
    {
        // new entity
        $user = new User();

        // Use the setPicturePath method
        $user->setPicturePath('olsen.jpg');

        // Assert the result
        $this->assertEquals('olsen.jpg', $user->getPicturePath());
    }

    // Check whether the setPhone function is working correctly
    public function testSetPhone()
    {
        // new entity
        $user = new User();

        // Use the setPhone method
        $user->setPhone('12312312');

        // Assert the result
        $this->assertEquals('12312312', $user->getPhone());
    }

    // Check whether the setUserName function is working correctly
    public function testSetUserName()
    {
        // new entity
        $user = new User();

        // Use the setUser_name method
        $user->setUserName('petjo123');

        // Assert the result
        $this->assertEquals('petjo123', $user->getUserIdentifier());
    }

    // Check whether the setFieldOfStudy function is working correctly
    public function testSetFieldOfStudy()
    {
        // new entity
        $user = new User();

        // dummy entity
        $fos = new FieldOfStudy();
        $fos->setName('BIT');

        // Use the setUser_name method
        $user->setFieldOfStudy($fos);

        // Assert the result
        $this->assertEquals($fos, $user->getFieldOfStudy());
    }

    // Check whether the addRole function is working correctly
    public function testAddRole()
    {
        // new entity
        $user = new User();

        // Use the addRole method
        $user->addRole('role1');

        // Roles is stored in an array
        $roles = $user->getRoles();

        // Loop through the array and check for matches
        foreach ($roles as $role) {
            $this->assertContains('role1', $roles);
        }
    }

    // Check whether the setNewUserCode function is working correctly
    public function testSetNewUserCode()
    {
        // new entity
        $user = new User();

        // Use the setNewUserCode method
        $user->setNewUserCode('secret');

        // Assert the result
        $this->assertEquals('secret', $user->getNewUserCode());
    }
}
