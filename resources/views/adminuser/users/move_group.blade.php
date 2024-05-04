<div id="modal-move-group" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="custom-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="float: left;">
                        <img id="creategroup-ico" src="{{ url('template/images/icon_menu/group.png') }}" width="24" height="24">
                    </div>
                    <div style="float: left; margin-left: 10px;">
                        <h4 class="modal-title" id="titleModal">
                            Move to group
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <form action="{{ route('adminuser.access-users.move-group')}}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id">
                    <br>
                    <select class="form-control select2" multiple name="group[]">
                        @foreach($group as $groups)
                            <option value="{{ $groups->group_id }}">{{ $groups->group_name }}</option>
                        @endforeach
                    </select>
                    <br>
                    <br>
                    <div class="formbutton">
                        <a class="cancelbtn" data-dismiss="modal">Cancel</a>
                        <button type="submit" class="createbtn">Move</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>