<?php

namespace App\Tests\Entity;

use App\Entity\Department;
use App\Entity\School;
use PHPUnit\Framework\TestCase;

class SchoolEntityUnitTest extends TestCase
{
    // Check whether the setName function is working correctly
    public function testSetName()
    {
        // new entity
        $school = new School();

        // Use the setName method
        $school->setName('Heggen');

        // Assert the result
        $this->assertEquals('Heggen', $school->getName());
    }

    // Check whether the setContactPerson unction is working correctly
    public function testSetContactPerson()
    {
        // new entity
        $school = new School();

        // Use the setContactPerson method
        $school->setContactPerson('Vibeke');

        // Assert the result
        $this->assertEquals('Vibeke', $school->getContactPerson());
    }

    // Check whether the setDepartment function is working correctly
    public function testSetDepartment()
    {
        // new entity
        $school = new School();

        // dummy entity
        $department1 = new Department();
        $department1->setName('NTNU');

        // Use the setDepartment method
        $school->setDepartment($department1);

        $school_dep = $school->getDepartment();

        $this->assertEquals($school_dep, $department1);
    }

    // Check whether the setEmail unction is working correctly
    public function testSetEmail()
    {
        // new entity
        $school = new School();

        // Use the setEmail method
        $school->setEmail('Heggen@vgs.com');

        // Assert the result
        $this->assertEquals('Heggen@vgs.com', $school->getEmail());
    }

    // Check whether the setPhone unction is working correctly
    public function testSetPhone()
    {
        // new entity
        $school = new School();

        // Use the setPhone method
        $school->setPhone('12312312');

        // Assert the result
        $this->assertEquals('12312312', $school->getPhone());
    }
}
