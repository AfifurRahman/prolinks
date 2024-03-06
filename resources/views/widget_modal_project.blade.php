<style>
    .modal-content {
        -webkit-border-radius: 0px !important;
        -moz-border-radius: 0px !important;
        border-radius: 10px !important; 
    }

    .modal-content {
        padding: 0px !important;
    }

    .modal-body {
        padding: 25px !important;
    }

    .custom-modal-header {
        padding: 5px;
        width: 95%;
        margin: 0 auto;
        margin-top: 13px;
    }

    .custom-form input {
        border-radius: 7px;
    }

    .custom-form select {
        border-radius: 7px;
    }

    .custom-form textarea {
        border-radius: 7px;
    }
</style>
<div id="modal-new-project" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="custom-modal-header">
                    <div style="float: left;">
                        <img src="{{ url('template/images/data-project.png') }}" width="24" height="24">
                    </div>
                    <div style="float: left; margin-left: 10px;">
                        <h4 class="modal-title" id="titleModal">
                            Create Project
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <form class="custom-form" action="{{ route('project.save-first-project') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Project Name <span class="text-danger">*</span></label>
                        <input required type="text" name="project_name" id="project_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Project Desc </label>
                        <textarea name="project_desc" id="project_desc" class="form-control"></textarea>
                    </div>
                    <div class="pull-right">
                        <button type="button" style="border-radius: 5px;" data-dismiss="modal" class="btn btn-default">
                            Close
                        </button>
                        <button type="submit" style="border-radius: 5px;" class="btn btn-primary">
                            Create Project
                        </button>
                    </div><div style="clear: both;"></div>			
                </form>
            </div>
        </div>
    </div>
</div>