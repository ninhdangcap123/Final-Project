<html>
<head>
    <title>Student Marksheet - <?php echo e($sr->user->name); ?></title>

    <link rel="stylesheet" type="text/css" href="<?php echo e(secure_asset('assets/css/my_print.css')); ?>" />
</head>
<body>
<div class="container">
    <div id="print" xmlns:margin-top="http://www.w3.org/1999/xhtml">
        
        <table width="100%">
            <tr>
                <td><img src="<?php echo e($s['logo']); ?>" style="max-height : 100px;"></td>

                <td style="text-align: center; ">
                    <strong><span style="color: #1b0c80; font-size: 25px;"><?php echo e(strtoupper(\App\Helpers\getSystemInfoHelper::getSetting('system_name'))); ?></span></strong><br/>
                   
                    <strong><span
                                style="color: #000; font-size: 15px;"><i><?php echo e(ucwords($s['address'])); ?></i></span></strong><br/>
                    <strong><span style="color: #000; font-size: 15px;"> REPORT SHEET <?php echo e('('.strtoupper($major->code).')'); ?>

                    </span></strong>
                </td>
                <td style="width: 100px; height: 100px; float: left;">
                    <img src="<?php echo e($sr->user->photo); ?>"
                         alt="..."  width="100" height="100">
                </td>
            </tr>
        </table>
        <br/>

        
        <div style="position: relative;  text-align: center; ">
            <img src="<?php echo e($s['logo']); ?>"
                 style="max-width: 500px; max-height:600px; margin-top: 60px; position:absolute ; opacity: 0.2; margin-left: auto;margin-right: auto; left: 0; right: 0;" />
        </div>

        
<?php echo $__env->make('pages.support_team.marks.print.sheet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        

        
        <?php echo $__env->make('pages.support_team.marks.print.skills', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div style="margin-top: 25px; clear: both;"></div>

        
        <?php echo $__env->make('pages.support_team.marks.print.comments', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('pages.support_team.marks.print.grading', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
</div>

<script>
    window.print();
</script>
</body>

</html>
<?php /**PATH D:\Final-Project\resources\views/pages/support_team/marks/print/index.blade.php ENDPATH**/ ?>