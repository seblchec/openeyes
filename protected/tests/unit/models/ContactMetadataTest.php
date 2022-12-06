<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ContactMetadataTest extends ActiveRecordTestCase
{
    /**
     * @var ContactMetadata
     */
    protected $model;
    public $fixtures = array(
        'contactmetadata' => 'ContactMetadata',
    );

    public function getModel()
    {
        return ContactMetadata::model();
    }

    public function dataProvider_Search()
    {
        return array(
            array(array('key' => 'gender'), 4, array('contactmetadata1', 'contactmetadata2', 'contactmetadata3', 'contactmetadata4')),
            array(array('value' => 'M'), 4, array('contactmetadata1', 'contactmetadata2', 'contactmetadata3', 'contactmetadata4')),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new ContactMetadata();
    }

    /**
     * @covers ContactMetadata
     */
    public function testModel()
    {
        $this->assertEquals('ContactMetadata', get_class(ContactMetadata::model()), 'Class name should match model.');
    }

    /**
     * @covers ContactMetadata
     */
    public function testTableName()
    {
        $this->assertEquals('contact_metadata', $this->model->tableName());
    }

    /**
     * @covers ContactMetadata
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->contactmetadata('contactmetadata3')->validate());
    }

    /**
     * @covers ContactMetadata
     * @dataProvider dataProvider_Search
     * @param $searchTerms
     * @param $numResults
     * @param $expectedKeys
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $this->model->setAttributes($searchTerms);
        $results = $this->model->search();
        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->contactmetadata($key);
            }
        }

        $this->assertEquals($numResults, $results->getItemCount());
        $this->assertEquals($expectedResults, $data);
    }
}
