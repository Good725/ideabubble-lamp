<?= (isset($alert)) ? $alert : '' ?>
<?php
if(isset($alert)){
?>
<script>
    remove_popbox();
</script>
<?php
}
?>
<table class="table table-striped" id="results_table">
    <thead>
        <tr>
            <th scope="col">Title</th>
            <th scope="col">Type</th>
            <th scope="col">Date</th>
            <th scope="col">Student</th>
            <th scope="col">Result</th>
            <th scope="col">Grade</th>
            <th scope="col">Comment</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
