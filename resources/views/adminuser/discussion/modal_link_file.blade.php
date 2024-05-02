<div id="modal-link-file" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="custom-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="float: left;">
                        <img src="{{ url('template/images/data-company.png') }}" width="24" height="24">
                    </div>
                    <div style="float: left; margin-left: 10px;">
                        <h4 class="modal-title" id="titleModal">
                            Select from dataroom
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <table id="tableLinksFiles" class="table table-hover">
                    @foreach($file as $files)
                        <tr>
                            <td><input type="checkbox" style="width:20px; height:20px;" value="{{ $files->id }}" data-filename="{{ $files->name }}" name="link_document" id="link_document" />&nbsp; <img src="{{ url('template/images/ext-file.png') }}" width="20" height="20"> {{ $files->name }} </td>
                        </tr>
                    @endforeach
                </table>
                <div class="pull-right">
                    <button type="button" class="btn btn-primary" onclick="getLinkDoc()">Apply</button>
                </div><div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>