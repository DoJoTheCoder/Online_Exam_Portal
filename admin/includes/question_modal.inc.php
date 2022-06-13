<!-- Contains all the modals used in question.php. -->

<!-- ###################################################### -->
<!-- Modal for adding questions -->
    <div class="modal fade" id="quesAddModal" tabindex="-1" aria-labelledby="quesAddModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quesAddModalLabel">Add question</h5>
                </div>
                <div class="modal-body">
                    <form action="phpScript/add.ps.php" method="post" autocomplete="off" id="quesAddForm">
                        <input type="hidden" name="quizid" value="<?php echo $_GET['quizid']; ?>">
                        <input class="form-control" type="text" name="ques_name" placeholder="Question">
                        <input class="form-control" type="text" name="opt1" placeholder="Option 1">
                        <input class="form-control" type="text" name="opt2" placeholder="Option 2">
                        <input class="form-control" type="text" name="opt3" placeholder="Option 3">
                        <input class="form-control" type="text" name="opt4" placeholder="Option 4">

                        <input class="form-control" type="text" name="opt_ans" placeholder="Correct Option number(1-4)">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary" value="add_question" form="quesAddForm">Add</button>
                </div>
            </div>
        </div>
    </div>

<!-- ###################################################### -->
<!-- Modal for modifying questions -->
    <div class="modal fade" id="quesModifyModal" tabindex="-1" aria-labelledby="quesModifyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quesModifyModalLabel">Modify question</h5>
                </div>
                <div class="modal-body">
                    <form action="phpScript/modify.ps.php" method="post" autocomplete="off" id="quesModifyForm">

                        <input type="hidden" name="qid" id='qid'>
                        <input type="hidden" name="quizid" id='quizid'>

                        <input class="form-control" type="text" name="ques_name" id="ques_name" placeholder="Question">
                        <input class="form-control" type="text" name="opt1" id="opt1" placeholder="Option 1">
                        <input class="form-control" type="text" name="opt2" id="opt2" placeholder="Option 2">
                        <input class="form-control" type="text" name="opt3" id="opt3" placeholder="Option 3">
                        <input class="form-control" type="text" name="opt4" id="opt4" placeholder="Option 4">

                        <input class="form-control" type="text" name="opt_ans" id="opt_ans" placeholder="Correct Option number(1-4)">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary" value="mod_question" form="quesModifyForm">Modify</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calling modify modal when 'modify' button is pressed-->
    <script>
        var modify = document.querySelectorAll('.modbtn');
        modify.forEach(function(button){
            button.onclick = function(){
                var form = document.getElementById('quesModifyForm');

                // access the parent tr for corresponding row containing question details
                var tr = this.parentElement.parentElement;
                var inputs = tr.querySelectorAll('input');

                // filling modal form with question details
                form.qid.value = inputs[0].value;
                form.quizid.value = inputs[1].value;
                form.ques_name.value = inputs[2].value;
                form.opt1.value = inputs[3].value;
                form.opt2.value = inputs[4].value;
                form.opt3.value = inputs[5].value;
                form.opt4.value = inputs[6].value;
                form.opt_ans.value = inputs[7].value;

                var modifyModal = new bootstrap.Modal(document.getElementById('quesModifyModal'));
                // show modal
                modifyModal.show();
            }
        });
    </script>

<!-- ###################################################### -->
<!-- Modal for deleting questions -->
    <div class="modal fade" id="quesDeleteModal" tabindex="-1" aria-labelledby="quesDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quesDeleteModalLabel">Delete question?</h5>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the question?</p>
                    <p>This will delete all relevant info related to the questions too.</p>
                    <form method="post" action="phpScript/delete.ps.php" id="quesDeleteForm">
                        <input type = "hidden" name="qid" id="qid">
                        <input type="hidden" name="quizid" id='quizid'>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-danger" value="del_question" form="quesDeleteForm">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to link to delete modal -->
    <script>
        var deletebtn = document.querySelectorAll('.delbtn');
        deletebtn.forEach(function(button){
            button.onclick = function(){
                var form = document.getElementById("quesDeleteForm");

                // access the parent tr for corresponding row containing question details
                var tr = this.parentElement.parentElement;
                var inputs = tr.querySelectorAll('input');
                
                // filling modal form with question details
                form.qid.value = inputs[0].value;
                form.quizid.value = inputs[1].value;

                var deleteModal = new bootstrap.Modal(document.getElementById('quesDeleteModal'));
                // show modal
                deleteModal.show();
            }
        });
    </script>

<!-- ###################################################### -->
<!-- Modal for uploading questions-->
    <div class="modal fade" id="quesUploadModal" tabindex="-1" aria-labelledby="quesUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quesUploadModalLabel">Upload questions?</h5>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to use this file for questions?</p>
                    <p>The current questions in the quiz will be replaced by the questions in the file.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-warning" value="upd_question" form="quesUploadForm">Upload</button>
                </div>
            </div>
        </div>
    </div>

<!-- ###################################################### -->
<!-- Modal for showing the excel file format -->
    <div class="modal fade" id="excelFormatModal" tabindex="-1" aria-labelledby="excelFormatModalLabel" aria-hidden="true">
        <div class="modal-dialog mw-100 w-75" id="excelFormatModalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="excelFormatModalLabel">Excel File Format</h5>
                </div>
                <div class="modal-body">
                    <img src="../images/format.png" width="100%">
                    <p>
                        <ul>
                            <li>Do not leave any column, row or cell empty in the table.</li>
                            <li>Make sure the columns are in the order as shown above.</li>
                            <li>Highlighting and additional formating is optional and doesn't affect the upload.</li>
                        </ul>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<!-- ###################################################### -->
<!-- Modal for setting the invigilator details -->
    <div class="modal fade" id="invigilatorModal" tabindex="-1" aria-labelledby="invigilatorModalLabel" aria-hidden="true">
        <div class="modal-dialog" id="invigilatorModalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invigilatorModalLabel">Enter Invigilator ID and Password</h5>
                </div>
                <div class="modal-body">
                    <form action="phpScript/invigilation_update.ps.php" method="post" id="invigilatorModalForm">
                        <input type="hidden" name="quizid" value="<?php echo $quizid;?>">
                        <input class="form-control" type="text" name="invigil_id" placeholder="ID">
                        <input class="form-control" type="password" name="invigil_pwd" placeholder="Password">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary" form="invigilatorModalForm">Submit</button>
                </div>
            </div>
        </div>
    </div>