<style type="text/css">
    .dashboard-summary {
        margin-top: 24px;
    }

    .widget-prolinks-custom {

    }

    .widget-prolinks-custom img {
        float: left;
        margin-top: 12px;
    }

    .info-widget {
        float: left;
        margin-left: 10px;
    }

    .info-widget h3 {

    }

    .info-widget p {
        line-height: 0px;
    }

    .resume-viewed h3 {
        margin-bottom: 15px;
    }

    .resume-viewed th{
        background-color: #F5F5F5;
        border-radius: 2px;
    }

    .icon-img {
        width: 22px;
        height: 22px;
    }

    .borderless td, .borderless th {
        border: none !important;
    }
</style>

<div class="dashboard-summary">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card-box widget-prolinks-custom">
                <img src="{{ url('template/images/activities/document.png') }}">
                <div class="info-widget">
                    <h3>{{ $total_documents }}</h3>
                    <p>Total documents</p>
                </div> <div style="clear: both;"></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card-box widget-prolinks-custom">
                <img src="{{ url('template/images/activities/users.png') }}">
                <div class="info-widget">
                    <h3>{{ $total_users }}</h3>
                    <p>Total users</p>
                </div> <div style="clear: both;"></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card-box widget-prolinks-custom">
                <img src="{{ url('template/images/activities/questions.png') }}">
                <div class="info-widget">
                    <h3>{{ $total_qna }}</h3> 
                    <p>Total questions</p>
                </div> <div style="clear: both;"></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card-box widget-prolinks-custom">
                <img src="{{ url('template/images/activities/doc_size.png') }}">
                <div class="info-widget">
                    <h3 style="font-size:12px;">{{ $total_size }} <br> ({{App\Helpers\GlobalHelper::formatBytes((DB::table('pricing')->where('id', DB::table('clients')->where('client_id',$clients->client_id)->value('pricing_id'))->value('allocation_size')) - (DB::table('upload_files')->where('client_id', $clients->client_id)->sum('size')))}} free)</h3>
                    <p>Total document size</p>
                </div> <div style="clear: both;"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card-box">
                <div class="resume-viewed">
                    <h3>Most viewed documents</h3>
                    @if(true)
                        <div class="card-box1">
                            <center>
                                <img src="http://127.0.0.1:8000/template/images/empty_qna.png" width="300" />
                            </center>    
                        </div>
                    @else
                        <table class="table table-hover borderless">
                            <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th>Unique view</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img class="icon-img" src="{{ url('template/images/ext-file.png') }}"> Control of Quality and Regulatory...xls</td>
                                    <td>798</td>
                                </tr>
                                <tr>
                                    <td><img class="icon-img" src="{{ url('template/images/ext-file.png') }}"> Facilities Management.doc</td>
                                    <td>492</td>
                                </tr>
                                <tr>
                                    <td><img class="icon-img" src="{{ url('template/images/ext-file.png') }}"> External Audits.pdf</td>
                                    <td>447</td>
                                </tr>
                                <tr>
                                    <td><img class="icon-img" src="{{ url('template/images/ext-img.png') }}"> Employee Training and Developm...img</td>
                                    <td>274</td>
                                </tr>
                                <tr>
                                    <td><img class="icon-img" src="{{ url('template/images/ext-file.png') }}"> Internal Quality Audits.docs</td>
                                    <td>185</td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-box">
                <div class="resume-viewed">
                    <h3>Most active users</h3>
                    @if(true)
                        <div class="card-box1">
                            <center>
                                <img src="http://127.0.0.1:8000/template/images/empty_qna.png" width="300" />
                            </center>    
                        </div>
                    @else
                        <table class="table table-hover borderless">
                            <thead>
                                <tr>
                                    <th>User name</th>
                                    <th>Docs. accessed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img class="icon-img" src="{{ url('template/images/avatar.png') }}"> Yanuar Adhitia Tutkey</td>
                                    <td>994</td>
                                </tr>
                                <tr>
                                    <td><img class="icon-img" src="{{ url('template/images/avatar.png') }}"> Aryo Agung Benardi</td>
                                    <td>826</td>
                                </tr>
                                <tr>
                                    <td><img class="icon-img" src="{{ url('template/images/avatar.png') }}"> Deny Stefany Febri</td>
                                    <td>738</td>
                                </tr>
                                <tr>
                                    <td><img class="icon-img" src="{{ url('template/images/avatar.png') }}"> Christin Purnama</td>
                                    <td>600</td>
                                </tr>
                                <tr>
                                    <td><img class="icon-img" src="{{ url('template/images/avatar.png') }}"> Rizki Agung Maulana</td>
                                    <td>274</td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>