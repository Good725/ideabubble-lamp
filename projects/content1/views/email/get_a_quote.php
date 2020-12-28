<table>
    <tr><td>Name:</td><td><?=@$form['name']?></td></tr>
    <tr><td>Date of birth:</td><td><?=@$form['datepicker']?></td></tr>
    <tr><td>Address:</td><td><?=@$form['address']?></td></tr>
    <tr><td>Gender:</td><td><?=@$form['gender']?></td></tr>
    <tr><td>Phone:</td><td><?=@$form['phone1']?></td></tr>
    <tr><td>Occupation:</td><td><?=@$form['occupation']?></td></tr>
    <tr><td>Email Address:</td><td><?=@$form['form_email']?></td></tr>
    <tr><td>Licence Type:</td><td><?=@$form['licence']?></td></tr>
<?  if(@$form['licence'] == "other"){
    echo "<tr><td>Licence Type:</td><td>".@$form['country_of_issue']."</td></tr>
          <tr><td>Licence Type:</td><td>".@$form['other_licence_type']."</td></tr>";
}?>
    <tr><td>Date of issue:</td><td><?=@$form['date_of_issue']?></td></tr>
    <tr><td>Insurance History:</td><td><?=@$form['insurance_history']?></td></tr>
<?  if(@$form['insurance_history'] != "0"){
    echo "<tr><td>Previous Insurer</td><td>".@$form['old_insurer_name']."</td></tr>";
}
?>
    <tr><td>Claims Bonus:</td><td><?=@$form['ncb']?></td></tr>
    <tr><td>Country of NCB:</td><td><?=@$form['ncb_country']?></td></tr>
    <tr><td>Claims in 5 years:</td><td><?=@$form['claims']?></td></tr>
    <tr><td>Penalty Points:</td><td><?=@$form['penalty_points']?></td></tr>
    <tr><td>Driving Offences:</td><td><?=@$form['offence_']?></td></tr>
<?  if(@$form['offence_'] == "yes"){
    echo "<tr><td>Description: </td><td>".@$form['driving_offence']."</td></tr>";
}
?>
    <tr><td>Start date of insurance:</td><td><?=@$form['date_start']?></td></tr>
    <tr><td>Type of insurance:</td><td><?=@$form['insurance_type']?></td></tr>
    <tr><td>Vehicle registration:</td><td><?=@$form['car_reg']?></td></tr>
    <tr><td>Make of car:</td><td><?=@$form['car_model']?></td></tr>
    <tr><td>Model of car:</td><td><?=@$form['car_make']?></td></tr>
    <tr><td>Fuel Type:</td><td><?=@$form['fuel_type']?></td></tr>
    <tr><td>Engine Size:</td><td><?=@$form['engine_size']?></td></tr>
    <tr><td>Measurement of engine size:</td><td><?=@$form['measure_type']?></td></tr>
    <tr><td>Value of car:</td><td><?=@$form['car_value']?></td></tr>
    <tr><td>Import/Modifications:</td><td><?=@$form['import_modifications']?></td></tr>
    <tr><td>Additional Drivers:</td><td><?=@$form['additional_drivers']?></td></tr>
    <?php
    if(@$form['additional_drivers'] > 0)
    {
        $count = $form['additional_drivers'];
        if($count >= 1)
        {
            echo "<tr><td>1st Named Driver</td><td></td></tr>
                  <tr><td>Named Driver Name:</td><td>".@$form['1st_name']."</td></tr>
                  <tr><td>Named Driver DOB:</td><td>".@$form['1st_dob']."</td></tr>
                  <tr><td>Named Driver Address:</td><td>".@$form['1st_address']."</td></tr>
                  <tr><td>Named Driver Gender:</td><td>".@$form['1st_gender']."</td></tr>
                  <tr><td>Named Driver Phone:</td><td>".@$form['1st_phone']."</td></tr>
                  <tr><td>Named Driver Occupation:</td><td>".@$form['1st_occupation']."</td></tr>
                  <tr><td>Named Driver Email:</td><td>".@$form['1st_email']."</td></tr>
                  <tr><td>Named Driver Licence Type:</td><td>".@$form['1st_licence']."</td></tr>
                  <tr><td>Named Driver Convictions:</td><td>".@$form['1st_convictions']."</td></tr>
                  <tr><td>Named Driver Claims:</td><td>".@$form['1st_claims']."</td></tr>";
        }
        if($count >= 2)
        {
            echo "<tr><td>2nd Named Driver</td><td></td></tr>
                  <tr><td>Named Driver Name:</td><td>".@$form['2nd_name']."</td></tr>
                  <tr><td>Named Driver DOB:</td><td>".@$form['2nd_dob']."</td></tr>
                  <tr><td>Named Driver Address:</td><td>".@$form['2nd_address']."</td></tr>
                  <tr><td>Named Driver Gender:</td><td>".@$form['2nd_gender']."</td></tr>
                  <tr><td>Named Driver Phone:</td><td>".@$form['2nd_phone']."</td></tr>
                  <tr><td>Named Driver Occupation:</td><td>".@$form['2nd_occupation']."</td></tr>
                  <tr><td>Named Driver Email:</td><td>".@$form['2nd_email']."</td></tr>
                  <tr><td>Named Driver Licence Type:</td><td>".@$form['2nd_licence']."</td></tr>
                  <tr><td>Named Driver Convictions:</td><td>".@$form['2nd_convictions']."</td></tr>
                  <tr><td>Named Driver Claims:</td><td>".@$form['2nd_claims']."</td></tr>";
        }
        if($count >= 3)
        {
            echo "<tr><td>3rd Named Driver</td><td></td></tr>
                  <tr><td>Named Driver Name:</td><td>".@$form['3rd_name']."</td></tr>
                  <tr><td>Named Driver DOB:</td><td>".@$form['3rd_dob']."</td></tr>
                  <tr><td>Named Driver Address:</td><td>".@$form['3rd_address']."</td></tr>
                  <tr><td>Named Driver Gender:</td><td>".@$form['3rd_gender']."</td></tr>
                  <tr><td>Named Driver Phone:</td><td>".@$form['3rd_phone']."</td></tr>
                  <tr><td>Named Driver Occupation:</td><td>".@$form['3rd_occupation']."</td></tr>
                  <tr><td>Named Driver Email:</td><td>".@$form['3rd_email']."</td></tr>
                  <tr><td>Named Driver Licence Type:</td><td>".@$form['3rd_licence']."</td></tr>
                  <tr><td>Named Driver Convictions:</td><td>".@$form['3rd_convictions']."</td></tr>
                  <tr><td>Named Driver Claims:</td><td>".@$form['3rd_claims']."</td></tr>";
        }
        if($count >= 4)
        {
            echo "<tr><td>4th Named Driver</td><td></td></tr>
                  <tr><td>Named Driver Name:</td><td>".@$form['4rd_name']."</td></tr>
                  <tr><td>Named Driver DOB:</td><td>".@$form['4rd_dob']."</td></tr>
                  <tr><td>Named Driver Address:</td><td>".@$form['4rd_address']."</td></tr>
                  <tr><td>Named Driver Gender:</td><td>".@$form['4rd_gender']."</td></tr>
                  <tr><td>Named Driver Phone:</td><td>".@$form['4rd_phone']."</td></tr>
                  <tr><td>Named Driver Occupation:</td><td>".@$form['4rd_occupation']."</td></tr>
                  <tr><td>Named Driver Email:</td><td>".@$form['4rd_email']."</td></tr>
                  <tr><td>Named Driver Licence Type:</td><td>".@$form['4rd_licence']."</td></tr>
                  <tr><td>Named Driver Convictions:</td><td>".@$form['4rd_convictions']."</td></tr>
                  <tr><td>Named Driver Claims:</td><td>".@$form['4rd_claims']."</td></tr>";
        }
    }
    ?>
</table>