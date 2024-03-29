<x-mail::message>
    <b>Hi {{ $fullname }}</b>,<br>
    Đã có yêu cầu thay đổi mật khẩu của bạn!<br>
    Nếu bạn không thực hiện yêu cầu này thì vui lòng bỏ qua email này.<br>
    Nếu không, vui lòng nhấp vào liên kết này để thay đổi mật khẩu của bạn:<br>
    <x-mail::button :url="''">
        Đổi mật khuẩu
    </x-mail::button>
</x-mail::message>