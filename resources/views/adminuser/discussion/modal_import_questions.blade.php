<div id="modal-import-questions" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="custom-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="titleModal">
                        Import Question
                    </h4>
                </div>
            </div>
            <div class="modal-body">
                <form id="form-import" enctype="multipart/form-data">
                    @csrf
                    <div class="body-template-import">
                        <a href="{{ url('template/template_import_qna/discussion_import_template.csv') }}" download="discussion_import_template.csv" class="btn btn-default" style="width:100%; text-align:left;">
                            <span style="color:#1570EF;">Template file.csv</span> <br>
                            <span style="color:#586474;">Download this file as a starting point of your file</span>
                            <div class="pull-right">
                                <i class="fa fa-download"></i>
                            </div>
                        </a> <br><br>
                        <input type="file" name="upload_qna" id="upload_qna" accept=".csv" class="form-control">
                        <span class="text-muted">Supported format: CSV</span>
                        <span class="text-muted pull-right">Maximum size: 15MB</span>
                    </div> <br>
                    <div class="pull-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button id="actSubmitImport" type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>