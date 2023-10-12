@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card" id="chat2">
                <div class="card-body" data-mdb-perfect-scrollbar="true" style="position: relative; height: 400 px">
                    @if(!empty($chats))
                    @foreach($chats as $chat)

                    <div class="d-flex flex-row justify-content-start list" style="margin-top: 30px; padding:10px" data-user-to-msg="{{$chat['chat_with_user_id']}}">
                        <p style="display:none" id>{{$chat['chat_with_user_id']}}</p>
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp" alt="avatar 1" style="width: 45px; height: 100%;" id="user_id_{{$chat['chat_with_user_id']}}">
                        <div>
                            <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;"><strong>{{
                                $chat['name']
                            }}</strong></p>
                            <p class="small ms-3 mb-3 rounded-3 text-muted">{{ date('H:s',strtotime($chat['created_at'])) }}</p>

                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>

            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('message'))
                    <div class="alert alert-success" role="alert">
                        {{ session('message') }}
                    </div>
                    @php
                    Session::forget('message')
                    @endphp

                    @endif
                    <section>
                        <div class="container py-5" id="app">


                            <div class="card" id="chat2">
                                <div class="card-header d-flex justify-content-between align-items-center p-3">
                                    <h5 class="mb-0">Chat</h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-mdb-ripple-color="dark">Let's Chat
                                        App</button>
                                </div>
                                <div class="card-body chat-inbox" data-mdb-perfect-scrollbar="true" style="position: relative; height: 400 px">

                                </div>
                                <form action="{{ route('chat') }}" method="post">
                                    @csrf
                                    <div class="card-footer text-muted d-flex justify-content-start align-items-center p-3">
                                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp" alt="avatar 3" style="width: 40px; height: 100%;">
                                        <input type="text" class="form-control form-control-lg" id="inputValue" placeholder="Type message" name="message">
                                        <button type="submit" class="ms-1 text-muted" style="padding: 10px;border:none;background:transparent"><i class="fa fa-paper-plane"></i></button>
                                    </div>
                                </form>
                            </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<script>
    $(document).ready(function() {
        $('form').submit(function(e) {
            e.preventDefault();
            let inputValue = $('#inputValue').val();
            console.log($('.small p-2 ms-3 mb-1 rounded-3 chat-room').data('idVal'));
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ url('/chat') }}",
                data: {
                    message: inputValue

                },
                dataType: 'json',
                success: function(data) {
                    let htmlSyntax = `<div class="d-flex flex-row justify-content-end">
                                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp" alt="avatar 1" style="width: 45px; height: 100%;">
                                        <div>
                                            <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;">${inputValue}</p>

                                            <p class="small ms-3 mb-3 rounded-3 text-muted">${data.time}</p>
                                        </div>
                                    </div>`;
                    $('.card-body.chat-inbox').append(htmlSyntax);
                    $('#inputValue').val('');

                }

            })
        })


        $('.d-flex.flex-row.justify-content-start.list').on('click', function() {
            let touser = $(this).attr('data-user-to-msg');
            console.log(touser);
            $('.card-body.chat-inbox').html('');

            $.ajax({
                type: 'GET',
                data: {
                    isAjax: true,
                    toUserId: touser
                },
                url: "{{ url('/home') }}",
                dataType: 'json',
                success: function(data) {


                    for (const key in data.messages) {
                        if (Object.hasOwnProperty.call(data.messages, key)) {
                            let element = data.messages[key];
                            // console.log('element is', element);
                            // console.log(element.from_id === data.loggedInUserId);
                            let htmlSyntax = `<div class="d-flex flex-row ${element.from_id === data.loggedInUserId ? 'justify-content-end' : 'justify-content-start'}">
                                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp" alt="avatar 1" style="width: 45px; height: 100%;">
                                        <div>
                                            <p class="small p-2 ms-3 mb-1 rounded-3 chat-rooms" style="background-color: #f5f6f7;" data-idVal=${touser}>${element.message}</p>


                                        </div>
                                    </div>`;

                            $('.card-body.chat-inbox').prepend(htmlSyntax);
                            $('.card-body.chat-inbox').data('userIdVal', element.to_id);
                            // console.log('to_id ', $('p .small.p-2.ms-3.mb-1.rounded-3.chat-rooms').data('idVal'));
                        }
                    }




                }
            })
        })

    })
</script>
@endsection