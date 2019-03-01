<?php

class BooksControllerTest extends TestCase
{
    /** @test */
    public function testIndexStatusCodeIs200()
    {
        $this->get('/books')->seeStatusCode(200);
    }

    /** @test */
    public function testShowReturnAValidBook()
    {
        $this->markTestIncomplete('Pending Test');
    }

    /** @test */
    public function testShowFailBookNotExists()
    {
        $this->markTestIncomplete('Pending Test');
    }
}