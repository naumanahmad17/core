<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Forms\Form;

if (isActionAccessible($guid, $connection2, '/modules/Staff/jobOpenings_manage_edit.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __('You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs
        ->add(__('Job Openings'), 'jobOpenings_manage.php')
        ->add(__('Edit Job Opening'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Check if school year specified
    $gibbonStaffJobOpeningID = $_GET['gibbonStaffJobOpeningID'];
    if ($gibbonStaffJobOpeningID == '') {
        echo "<div class='error'>";
        echo __('You have not specified one or more required parameters.');
        echo '</div>';
    } else {
        try {
            $data = array('gibbonStaffJobOpeningID' => $gibbonStaffJobOpeningID);
            $sql = 'SELECT * FROM gibbonStaffJobOpening WHERE gibbonStaffJobOpeningID=:gibbonStaffJobOpeningID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo __('The specified record cannot be found.');
            echo '</div>';
        } else {
            //Let's go!
            $values = $result->fetch();

            $form = Form::create('action', $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module']."/jobOpenings_manage_editProcess.php?gibbonStaffJobOpeningID=$gibbonStaffJobOpeningID");

            $form->setClass('smallIntBorder fullWidth');

            $form->addHiddenValue('address', $_SESSION[$guid]['address']);

            $types = array(__('Basic') => array ('Teaching' => __('Teaching'), 'Support' => __('Support')));
            $sql = "SELECT gibbonRoleID as value, name FROM gibbonRole WHERE category='Staff' ORDER BY name";
            $result = $pdo->executeQuery(array(), $sql);
            $types[__('System Roles')] = ($result->rowCount() > 0)? $result->fetchAll(\PDO::FETCH_KEY_PAIR) : array();
            $row = $form->addRow();
                $row->addLabel('type', __('Type'));
                $row->addSelect('type')->fromArray($types)->placeholder()->isRequired();

            $row = $form->addRow();
                $row->addLabel('jobTitle', __('Job Title'));
                $row->addTextField('jobTitle')->maxlength(100)->isRequired();

            $row = $form->addRow();
                $row->addLabel('dateOpen', __('Opening Date'));
                $row->addDate('dateOpen')->isRequired();

            $row = $form->addRow();
                $row->addLabel('active', __('Active'));
                $row->addYesNo('active')->isRequired();

            $row = $form->addRow();
                $column = $row->addColumn();
                $column->addLabel('description', __('Description'));
                $column->addEditor('description', $guid)->setRows(20)->showMedia()->isRequired();

            $form->loadAllValuesFrom($values);

            $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

            echo $form->getOutput();
        }
    }
}
