<?php
    require_once "includes/header.inc.php";
    require_once "includes/functions.inc.php";
    require_once "../includes/database.inc.php";
?>
<!-- page for adding and modifying subjects -->
<?php
    if(isset($_SESSION['admin_name'])){
        echo '<h2>Add Subject</h2>
        <form action="phpScript/add.ps.php" method="post" autocomplete="off">
            <input class="form-control" type="text" name="sub_name" placeholder="Subject Name">
            <button class="btn btn-primary" type="submit" name="submit" value="add_subject">Add</button>
        </form>'; 

        // error message check
        if(isset($_GET["error"])){
            echo '<div class="alert alert-warning">';

            if($_GET["error"] == "inputempty")
                echo 'Please fill in the field!';
            else if($_GET["error"] == "failed")
                echo 'Failed to add subject, please try again later.';
            else if($_GET["error"] == "subjectexists")
                echo 'Subject already exists!';

            echo '</div>';
        }

        // success message
        if(isset($_GET["result"]) && $_GET["result"] == "success"){
            echo '<div class="alert alert-success">Subject successfully added.</div>';
        } 
        
        // modifying existing subject
        echo '<br><h2>Change subject name</h2>
        <form action="phpScript/modify.ps.php" method="post" autocomplete="off">
            <input class="form-control"list="subjects" name="oldsubname" placeholder="Choose a subject">
            <datalist id="subjects">';
            
        $sql = "SELECT * FROM subjects;";
        $subList = mysqli_query($conn, $sql);
        if(mysqli_num_rows($subList)>0){
            while($row = mysqli_fetch_assoc($subList)){
                echo '<option value = "'.$row['subName'].'">';
            }
        }
        echo '</datalist>
            <input class="form-control"type="text" name="newsubname" placeholder="Changed subject name" >
            <button class="btn btn-primary" type="submit" name="submit" value="mod_subject">Change</button>
        </form>';

        // error message check
        if(isset($_GET["error2"])){
            echo '<div class="alert alert-warning">';

            if($_GET["error2"] == "inputempty")
                echo 'Please fill in the field!';
            else if($_GET["error2"] == "failed")
                echo 'Failed to make changes, please try again later.';
            else if($_GET["error2"] == "subjectexists")
                echo 'The changed subject name already exists!';
            
            echo '</div>';
        }

        // success message
        if(isset($_GET["result2"]) && $_GET["result2"] == "success"){
            echo '<div class="alert alert-success">Subject successfully changed.</div>';
        } 

        // Delete existing subject
        echo '<br><h2>Delete subject</h2>
        <form action="javascript:confirmDelete(sub.value)" autocomplete ="off">
            <input class="form-control" list="subjects" id="sub" placeholder="Choose a subject">
            <datalist id="subjects">';
            
        $sql = "SELECT * FROM subjects;";
        $subList = mysqli_query($conn, $sql);
        if(mysqli_num_rows($subList)>0){
            while($row = mysqli_fetch_assoc($subList)){
                echo '<option value = "'.$row['subName'] .'">';
            }
        }
        echo '</datalist>
            <button class="btn btn-primary" type="submit" name="submit">Delete</button>
        </form>';

        // error message check
        if(isset($_GET["error3"])){
            echo '<div class="alert alert-warning">';

            if($_GET["error3"] == "inputempty")
                echo 'Please fill in the field!';
            else if($_GET["error3"] == "failed")
                echo 'Failed to delete, please try again later.';
            else if($_GET["error3"] == "notexist")
                echo 'Deletion failed. Subject doesn\'t exist.';

            echo '</div>';
        }

        // success message
        if(isset($_GET["result3"]) && $_GET["result3"] == "success"){
            echo '<div class="alert alert-success">Subject successfully deleted.</div>';
        } 

    }
    else{
        header("location: index.php");
        exit();
    }
?>

<!-- modal pop-up for confirming delete -->
<div id="deleteModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Delete Subject?</h4>
            </div>

            <div class="modal-body">
                <p>Are you sure you want to delete the subject?</p>
                <p>This will delete the subject with all the relevant quizes and questions under the subject.</p>
                <form method="post" action="phpScript/delete.ps.php" id="form-delete-subject">
                    <input class="form-control"type = "hidden" name="sub_name">
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="submit" form="form-delete-subject" class="btn btn-danger" value="del_subject">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- script for confirming delete -->
<script>
    function confirmDelete(sub){
        document.getElementById("form-delete-subject").sub_name.value = sub;
        var deleteModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>

<?php
    include_once "../includes/footer.inc.php";
?>



