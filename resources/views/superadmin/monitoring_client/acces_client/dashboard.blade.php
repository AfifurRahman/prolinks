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
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="card-box widget-prolinks-custom">
                <img src="{{ url('template/images/activities/document.png') }}">
                <div class="info-widget">
                    <h3>{{ $total_documents }}</h3>
                    <p>Total documents</p>
                </div> <div style="clear: both;"></div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="card-box widget-prolinks-custom">
                <img src="{{ url('template/images/activities/users.png') }}">
                <div class="info-widget">
                    <h3>{{ $total_users }}</h3>
                    <p>Total users</p>
                </div> <div style="clear: both;"></div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="card-box widget-prolinks-custom">
                <img src="{{ url('template/images/activities/questions.png') }}">
                <div class="info-widget">
                    <h3>{{ $total_qna }}</h3> 
                    <p>Total questions</p>
                </div> <div style="clear: both;"></div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="card-box widget-prolinks-custom">
                <img src="{{ url('template/images/activities/doc_size.png') }}">
                <div class="info-widget">
                    <h3 style="font-size:12px;">{{ $total_size }} <br> ({{App\Helpers\GlobalHelper::formatBytes((DB::table('pricing')->where('id', DB::table('clients')->where('client_id',$clients->client_id)->value('pricing_id'))->value('allocation_size')) - (DB::table('upload_files')->where('client_id', $clients->client_id)->sum('size')))}} free)</h3>
                    <p>Total document size</p>
                </div> <div style="clear: both;"></div>
            </div>
        </div>
    </div>
</div>