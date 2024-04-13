@extends('layout.default')
@section('content')

    <div class="container">
        <div class="row form-group">
            @include('elements.user-menu',array('page'=>'user_profile'))
            <div class="col-sm-12 grey-bg">
                <div class="row">
                    <div class="col-sm-7">
                        <h1><span class="glyphicon glyphicon-user"></span> &nbsp; <strong>John Doe</strong></h1><br /><br /><br />
                        <button class="btn orange-bg"><span class="glyphicon glyphicon-send"></span> &nbsp; Send Private Message</button>
                    </div>
                    <div class="col-sm-5">
                        <div class="panel form-group marginTop10">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12"><strong>Society Points:</strong></div>
                                    <div class="col-xs-6">
                                        Last 6 months
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        3,000
                                    </div>
                                    <div class="col-xs-7">
                                        All time:
                                    </div>
                                    <div class="col-xs-5 text-right">
                                        50,000
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel form-group">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-8">
                                        <strong>Contribution Ranking:</strong>
                                    </div>
                                    <div class="col-xs-4 text-right text-gold">
                                        Gold
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel form-group">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <strong>Donations:</strong>
                                    </div>
                                    <div class="col-xs-7">
                                        Donations Received:
                                    </div>
                                    <div class="col-xs-5 text-right">
                                        1,200 $
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default panel-dark-grey">
                    <div class="panel-heading">
                        <h4>Recent Activity</h4>
                    </div>
                    <div class="panel-body table-inner table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td>- Edited <strong>Objective 1</strong> for <strong>Unit 1</strong></td>
                                </tr>
                                <tr>
                                    <td>- Edited <strong>Objective 1</strong> for <strong>Unit 1</strong></td>
                                </tr>
                                <tr>
                                    <td>- Edited <strong>Objective 1</strong> for <strong>Unit 1</strong></td>
                                </tr>
                                <tr>
                                    <td>- Edited <strong>Objective 1</strong> for <strong>Unit 1</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('elements.footer')
@endsection