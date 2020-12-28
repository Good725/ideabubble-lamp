$(document).on('ready', function(){
    var smileys = [
		 {
            'code': 'O:-)',
            'alt': 'Angel',
            'img': 'angel.png'
        },
        {
			'code': ':@',
            'alt': 'Angry',
            'img': 'angry.png'
		},
		{
			'code': ':triumph:',
            'alt': 'Angry1',
            'img': 'angry-1.png'
		},
		{
			'code': '-_-',
            'alt': 'Bored',
            'img': 'bored.png'
		},
		{
			'code': 'o.O',
            'alt': 'Confused',
            'img': 'confused.png'
		},
		{
			'code': '8=)',
            'alt': 'Cool',
            'img': 'cool.png'
		},
		{
			'code': 'B|',
            'alt': 'Cool',
            'img': 'cool-1.png'
		},
		{
			'code': ';(',
            'alt': 'Crying',
            'img': 'crying.png'
		},
		{
			'code': ':cry:',
            'alt': 'Crying',
            'img': 'crying-2.png'
		},
		{
			'code': ':cute:',
            'alt': 'cute',
            'img': 'cute.png'
		},
		{
			'code': ':-$',
            'alt': 'Embarrassed',
            'img': 'embarrassed.png'
		},
		{
			'code': ':emoji:',
            'alt': 'emoji',
            'img': 'emoji.png'
		},
		{
			'code': ':greed:',
            'alt': 'greed',
            'img': 'greed.png'
		},
		{
			'code': '=)',
            'alt': 'happy',
            'img': 'happy.png'
		},
		{
			'code': ':-)',
            'alt': 'happy-1',
            'img': 'happy-1.png'
		},
		{
			'code': ':)',
            'alt': 'happy-2',
            'img': 'happy-2.png'
		},
		{
			'code': '^-^',
            'alt': 'happy-3',
            'img': 'happy-3.png'
		},
		{
			'code': '<3',
            'alt': 'in-love',
            'img': 'in-love.png'
		},
		{
			'code': ':*',
            'alt': 'kiss',
            'img': 'kiss.png'
		},
		{
			'code': ':D',
            'alt': 'Laugh',
            'img': 'laughing.png'
		},
		{
			'code': ':mute:',
            'alt': 'mute',
            'img': 'muted.png'
		},
		{
			'code': 'B)',
            'alt': 'nerd',
            'img': 'nerd.png'
		},
		{
			'code': ':(',
            'alt': 'Sad',
            'img': 'sad.png'
		},
		{
			'code': ':fearful:',
            'alt': 'scare',
            'img': 'scare.png'
		},
		{
			'code': ':serious:',
            'alt': 'serious',
            'img': 'serious.png'
		},
		{
			'code': ':O',
            'alt': 'shocked',
            'img': 'shocked.png'
		},
		{
			'code': ':-&',
            'alt': 'sick',
            'img': 'sick.png'
		},
		{
			'code': '|-)',
            'alt': 'Sleepy',
            'img': 'sleepy.png'
		},
		{
			'code': ':smart:',
            'alt': 'Smart',
            'img': 'smart.png'
		},
		{
			'code': '(@@)',
            'alt': 'Suspicious',
            'img': 'suspicious.png'
		},
		{
			'code': ':P',
            'alt': 'Tongue',
            'img': 'tongue.png'
		},
		{
			'code': ':vain:',
            'alt': 'Vain',
            'img': 'vain.png'
		},
		{
			'code': ';)',
            'alt': 'Wink',
            'img': 'wink.png'
		}
    ];

    var refresh_seconds = 3;
    var get_data_timeout = null;
    var panel = $("#chat-panel");
    var search = panel.find('.search');
    var users = panel.find('.users');
    users.list = {};
    var rooms = {};
    var ignore_rooms = [];

    var user_template = users.find('.user.template');
    user_template.remove();

    var room_template = $(".chat-room.template");
    room_template.remove();

    var message_template = room_template.find('.message.template');
    room_template.remove();

    var get_data_first = true;
    get_data();
    function set_get_data()
    {
        if (get_data_timeout) {
            clearTimeout(get_data_timeout);
        }
        get_data_timeout = setTimeout(get_data, refresh_seconds * 1000);
    }

    function get_data(action, display_loader, callback)
    {
        if (get_data_timeout) {
            clearTimeout(get_data_timeout);
        }
        get_data_timeout = false;

        var prev_xhr_hide = window.disableScreenDiv.hide;
        window.disableScreenDiv.hide = display_loader ? true : false;

        var data = {};
        if (get_data_first) {
            data.load_all_messages = 1;
            get_data_first = false;
        }
        if (action) {
            data.action = action;
        }

        data.open_room = $('.chat-room:visible').data('room-id');

        $.post(
            '/admin/chat/get_data',
            data,
            function (response) {
                if (callback) {
                    callback(response);
                }
                update_rooms_list(response.rooms);
                update_users_list(response.users);
                if (data.open_room) {
                    update_messages(response.messages);
                }
                set_get_data();
            }
        );

        window.disableScreenDiv.hide = prev_xhr_hide;
    }

    function leave_room(id)
    {
        var room = rooms[id];
        room.hide();
        var params = {action: 'leave', room_id: id};
        get_data(
            params,
            true,
            function (response) {
                room.remove();
                room = null;
                delete rooms[params.room_id];
            }
        );
    }

    function create_room(name, is_public, id, join, hidden)
    {
        var room = null;
        if (id) {
            room = rooms[id];
        }

        if (!room) {
            room = room_template.clone();
            room.removeClass("template");
            room.joined_users = join;
            room.data("name", name);
            //$(document.body).append(room);
            panel.find('.chat-rooms').append(room);
            room.find('.header h2').html(name);
            room.find('.header .close').on('click', function (){
                leave_room(room.data('room-id'));
            });
            function send_message()
            {
                var text = room.find('input.message').val();
                if (text != "") {
                    get_data({action: 'message', room_id: room.data('room-id'), text: text}, true);
                    room.find('input.message').val('');
                }
            }
            room.find('button.send').on('click', function(){
                send_message();
            });
            room.find('input.message').on('keypress', function(e) {
                if (e.keyCode == 13) { // enter
                    send_message();
                }
            });

            room.on('click', function (){
                for (var i in rooms) {
                    rooms[i].addClass("hidden");
                }
                room.removeClass("hidden");
            });

            if (!id) {
                get_data(
                    {action: 'room', room: name, is_public: is_public, join: join},
                    true,
                    function (response) {
                        id = response.room_id;
                        room.data('room-id', id);
                        rooms[id] = room;
                    }
                );
            } else {
                room.data('room-id', id);
                rooms[id] = room;
            }
        }
        if (!hidden) {
            room.show();
            //$('#chat-panel-collapsed').addClass('hidden');
            //$('#chat-panel').removeClass('hidden');
        }
        return room;
    }

    function update_rooms_list(rooms_data)
    {
        for(var i in rooms_data) {
            if (!rooms[rooms_data[i].id]) {
                var already_joined = false;
                for (var j in rooms_data[i].users){
                    if (rooms_data[i].users[j].id == ibcms.user.id && rooms_data[i].users[j].joined != null) {
                        already_joined = true;
                        break;
                    }
                }
                if (rooms_data[i].created_by == ibcms.user.id || already_joined) {
                    create_room(rooms_data[i].name, rooms_data[i].is_public, rooms_data[i].id);
                } else {
                    if (rooms_data[i].invited != null && rooms_data[i].joined == null) {
                        create_room(rooms_data[i].name, null, rooms_data[i].id, null, true);
                        join_room_warning(rooms_data[i].id, rooms_data[i].name);
                    }
                }
            } else {
                if (!rooms[rooms_data[i].id].data('room-id')) {
                    rooms[rooms_data[i].id].data('room-id', rooms_data[i].id);
                }
            }
        }
    }

    function join_room_warning(id, name)
    {
        if (ignore_rooms.indexOf(id) == -1) {
            if (get_data_timeout) {
                clearTimeout(get_data_timeout);
            }
            create_room(name, null, id);
            rooms[id].hide();

            $("#chat_room_join .room-name").html(name);
            $("#chat_room_join").modal('show');
            $("#chat-room-join-yes").data('room-id', id);
            $("#chat-room-join-no").data('room-id', id);
        }
    }

    $('#chat-room-join-yes').on('click', function() {
        open_room($(this).data('room-id'));
    });

    $('#chat-room-join-no').on('click', function() {
        ignore_room($(this).data('room-id'));
    });

    $('.bulletin-chat-open').on('click', function() {
        var room_data = $(this).data();

        if (rooms[room_data.room_id]) {
            open_room(room_data.room_id);
        }
        else {
            join_room_warning(room_data.room_id, room_data.room_name);
        }

        $('#user-notifications-dropout').hide();
    });

    function open_room(room_id) {
        $('#chat-panel-collapsed').addClass('hidden');
        $('#chat-panel').removeClass('hidden');
        $('.chat-room').hide();
        rooms[room_id].show();
        get_data({action: 'join', room_id: room_id}, true);
        $('.user-chat-dropdown').show();
        rooms[room_id].find('.message').focus();
    }

    function ignore_room (room_id) {
        ignore_rooms.push(room_id);
        get_data({action: 'leave', room_id: room_id}, true);
    }

    function update_users_list(users_data)
    {
        for(var i in users_data) {
            if (users_data[i].id == window.ibcms.user.id) {
                continue;
            }

            add_user_to_list(users_data[i]);
        }
    }

    function add_user_to_list(user_data)
    {
        var user = null;
        user = users.list[user_data.email];
        if (!user) {
            user = user_template.clone();
            user.room = null;
            user.removeClass('template');
            users.append(user);
            user.data('user-id', user_data.id);
			user.data('room-id', user_data.room_id);
            user.data('email', user_data.email);
            user.data('name', user_data.name);
            user.find('.details').html(user_data.email);
            user.find('.avatar .image img').attr("src", user_data['avatar']);
            user.find('.send_message').on('click', function () {
				$('.users .user').removeClass('current-chat');
				$(this).parent().addClass('current-chat');
                if (user.room == null) {
                    user.room = create_room(user.data('name') + ' - ' + window.ibcms.user.name, 0, user_data.room_id, [user.data('user-id')]);
                }
            });
            user.on('click', function (){
                for (var i in rooms) {
                    rooms[i].addClass("hidden")
                }
                user.room.removeClass("hidden");
            });
            users.list[user_data.email] = user;

        }

        user.removeClass('online');
        user.removeClass('offline');
        user.addClass(user_data.online ? 'online' : 'offline');
        return user;
    }

    panel.find(".search input").autocomplete({
        select: function(e, ui) {
            create_room(ui.item.label, 0, null, [ui.item.value]);
            return false;
        },

        source: function(data, callback){
            $.get("/admin/chat/user_search",
                data,
                function(response){
                    callback(response);
                });
        }
    });

    function update_messages(messages)
    {
        var message_div = null;
        for (var i in messages) {
            message_div = add_message(messages[i]);
        }
        if (message_div) {
            message_div.focus(); // focus to last received message
        }
    }

    function add_message(message_data)
    {
        var message = message_template.clone();
        message.find('.text p').html(replace_smileys(message_data.message));
        message.find('.user .avatar img').attr('src', message_data.avatar);
        message.find('.user .avatar img').attr('title', message_data.username);
        message.find('.user .time').html(message_data.created);
        if (message_data.user_id == ibcms.user.id) {
            message.find('.user, .text').addClass('sent');
        } else {
            message.find('.user, .text').addClass('received');
        }

        var room = null;
        for (var i in rooms) {
            if (rooms[i].data('room-id') == message_data.room_id) {
                rooms[i].find('.body').append(message);
                for (var j in users.list) {
                    if (users.list[j].room == rooms[i]) {
                        $(users.list[j]).find('.last-msg .text').html(message_data.message);
                    }
                }
            }
        }
        return message;
    }

    $("#chat-panel-collapsed").on('click', function(){
        var $expanded_area = $('#chat-panel');
        var $collapsed_area = $('#chat-panel-collapsed');

        if ($collapsed_area.hasClass('hidden'))
        {
            $expanded_area.addClass('hidden');
            $collapsed_area.removeClass('hidden');
        }
        else
        {
            $collapsed_area.addClass('hidden');
            $expanded_area.removeClass('hidden');
        }
    });

    $("#chat-panel .pclose").on('click', function(){
        var $expanded_area = $('#chat-panel');
        var $collapsed_area = $('#chat-panel-collapsed');

        if ($collapsed_area.hasClass('hidden'))
        {
            $expanded_area.addClass('hidden');
            $collapsed_area.removeClass('hidden');
        }
        else
        {
            $collapsed_area.addClass('hidden');
            $expanded_area.removeClass('hidden');
        }
    });

    function replace_smileys(text)
    {
        var dir = $("#chat-panel").data("assets-dir");
        for (var i in smileys) {
            var img = '<img src="' + dir + '/images/simileys/' + smileys[i].img + '" alt="' + smileys[i].alt + '" />';
            text = text.replace(smileys[i].code, img);
        }

        return text;
    }

    $(document).on("click", ".sending-icon .smiley", function(){
        var code = $(this).data("code");
        var $input = $(this).parents(".chat-room").find("input.message");
        $input.val($input.val() + code);
    });

});

