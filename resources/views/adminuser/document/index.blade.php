@extends('layouts.app_client')

<style>
    .table {
        border-radius:0px;
        overflow:auto;
    }

    .tableDocument thead th:first-child {
        border-top-left-radius: 10px;
    }
    
    .tableDocument thead th:last-child {
        border-top-right-radius: 10px;
    }

    .tableDocument {
        border-collapse: separate;
        border:1px solid #CED5DD;
        border-radius: 7px;
        width:100%;
    }

    .tableDocument th {
        padding: 17px 0px 15px 10px;
        border-bottom: 1px solid #D0D5DD;
        background: #F9FAFB;
        font-size:15px;
        font-weight:600;
    }

    .tableDocument td {
        padding: 8px 0px 6px 10px;
        border-bottom:1px solid #CED5DD;
        font-size:13.5px;
        color:black;
    }

    .tableDocument tbody tr:last-child td {
        border-bottom: none;
    }

    .box_helper{
        margin-bottom:10px;
        display:flex;
        width:100%;
        justify-content: space-between;
    }

    .filter_button{
        padding:7px 15px 6px 17px;
        background: #FFFFFF; 
        color:#546474;
        border:1px solid #EDF0F2;
        border-radius:10px;
    }

    .filter_icon{
        margin-top:-1px;
        margin-right:4px;
        height:23px;
        width:20px;
    }

    .searchbox{
        width:22%;
        padding:8px 10px 5px 12px;
        border:1px solid #CED5DD;
        border-radius: 8px;
    }

    .searchbar{
        border:none;
    }

    .search_icon{
        width:19px;
        height:19px;
        margin-top:-3px;
        margin-right:4px;
    }

    .createfolder {
        color:#0072EE;
        border:1px solid #EDF0F2;
        border-radius:9px;  
        height:38px;
        background:#FFFFFF;   
        margin-top:10px;
        margin-right:6px;
        padding:8px 19px 7px 14px;
    }

    .export {
        color:#586474;
        border:none;
        background:#FFFFFF;
        margin-top:10px;
        margin-right:6px;
        padding:8px 19px 7px 14px;   
    }

    .upload {
        color:#FFFFFF;
        border:none;
        border-radius:9px;
        height:38px;
        background:#0072EE;
        margin-top:10px;
        padding:8px 19px 7px 14px;
    }

    .upload_ico {
        height:19px;
        width:20px;
        margin-top:-2px;
        margin-left:4px;
        margin-right:12px;
    }

    .fol-fil-icon{
        height:16.5px;
        width:20px;
        margin-right:8px;
        margin-top:6px;
        margin-bottom:8px;
    }
    
</style>

@section('notification')
    @if(session('notification'))
        <div class="notificationlayer">
            <div class="notification" id="notification">
                <image class="notificationicon" src="{{ url('template/images/icon_menu/checklist.png') }}"></image>
                <p class="notificationtext">{{ session('notification') }}</p>
            </div>
        </div>
    @endif
@endsection

@section('content')
    <div class="box_helper">
        <h2 id="title" style="color:black;font-size:28px;">Documents</h2>
        <div class="button_helper">
            <button class="export">Export</button>
            <button class="createfolder">Create folder</button>  
            <button class="upload"><image class="upload_ico" src="{{ url('template/images/icon_menu/upload.png') }}"></image>Upload</button>
        </div>
    </div>

    <div class="box_helper">
        <div>
            <button class="filter_button">
                <image class="filter_icon" src="{{ url('template/images/icon_menu/filter.png') }}"></image>
                Filter
            </button>
        </div>
        <div class="searchbox">
            <image class="search_icon" src="{{ url('template/images/icon_menu/search.png') }}"></image>
            <input type="text" class="searchbar" placeholder="Search documents...">
        </div>
    </div>

    <div class="table">
        <table class="tableDocument">
            <thead>
                <tr>
                    <th id="check">Index</th>
                    <th id="name">File name</th>
                    <th id="company">Created at</th>
                    <th id="role">Size</th> 
                    <th id="navigationdot">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <a class="fol-fil" href="#">
                            <image class="fol-fil-icon" src="{{ url('template/images/icon_menu/foldericon.png') }}" />
                            Videos
                        </a>
                    </td>
                    <td>11 Feb 2024, 19.38</td>
                    <td>26Mb</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

@endsection