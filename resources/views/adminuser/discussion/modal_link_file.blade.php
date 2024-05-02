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
                @if(count($file) > 0)
                    <table id="tableLinksFiles" class="table table-hover">
                        @foreach($file as $files)
                            <tr>
                                <td><input type="checkbox" style="width:20px; height:20px;" value="{{ $files->id }}" data-filename="{{ $files->name }}" name="link_document" id="link_document" />&nbsp; <img src="{{ url('template/images/ext-file.png') }}" width="20" height="20"> {{ $files->name }} </td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <div class="card-box">
                        <center>
                            <img src="{{ url('template/images/empty_qna.png') }}" width="300" />
                        </center>    
                    </div>
                @endif
                <div class="pull-right">
                    <button type="button" data-dismiss="modal" class="btn btn-default" style="border-radius: 5px;">
                        Close
                    </button>
                    @if(count($file) > 0)
                        <button type="button" class="btn btn-primary" style="border-radius: 5px;" onclick="getLinkDoc()">Apply</button>
                    @endif
                </div><div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>