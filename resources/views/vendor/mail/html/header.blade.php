@props(['url'])
<tr>
<td class="header">
{{--<a href="javascript:void(0);" style="display: inline-block;">--}}
@if (trim($slot) === 'Laravel')
<img src="https://xrplatform.kr/images/airpass.jpg" alt="대체 텍스트" width="10%">
@else
{{ $slot }}
@endif
{{--</a>--}}
</td>
</tr>
