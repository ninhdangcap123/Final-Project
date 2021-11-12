<div style="margin-bottom: 5px; text-align: center">
    <table border="0" cellpadding="5" cellspacing="5" style="text-align: center; margin: 0 auto;">
        <tr>
            <td><strong>KEY TO THE GRADING</strong></td>
            {{--            {{dd($major->id)}}--}}
            @if(\App\Helpers\PrintMarkSheetHelper::getGradeList($major->id)->count())
                @foreach(\App\Helpers\PrintMarkSheetHelper::getGradeList($major->id) as $gr)
                    <td><strong>{{ $gr->name }}</strong>
                        => {{ $gr->mark_from.' - '.$gr->mark_to }}
                    </td>
                @endforeach
            @endif
        </tr>
    </table>

</div>


<table style="width:100%; border-collapse:collapse; ">
    <tbody>
    <tr>
        <td><strong>NUMBER OF : </strong></td>
        <td><strong>Distinctions:</strong> {{ \App\Helpers\PrintMarkSheetHelper::countDistinctions($marks) }}</td>
        <td><strong>Credits:</strong> {{ \App\Helpers\PrintMarkSheetHelper::countCredits($marks) }}</td>
        <td><strong>Passes:</strong> {{ \App\Helpers\PrintMarkSheetHelper::countPasses($marks) }}</td>
        <td><strong>Failures:</strong> {{ \App\Helpers\PrintMarkSheetHelper::countFailures($marks) }}</td>
        <td><strong>Subjects Offered:</strong> {{ \App\Helpers\PrintMarkSheetHelper::countSubjectsOffered($marks) }}
        </td>
    </tr>

    </tbody>
</table>
