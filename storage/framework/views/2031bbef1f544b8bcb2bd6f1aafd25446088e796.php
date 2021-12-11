<div style="margin-bottom: 5px; text-align: center">
    <table border="0" cellpadding="5" cellspacing="5" style="text-align: center; margin: 0 auto;">
        <tr>
            <td><strong>KEY TO THE GRADING</strong></td>

            <?php if(\App\Helpers\printMarkSheetHelper::getGradeList($major->id)->count()): ?>
                <?php $__currentLoopData = \App\Helpers\printMarkSheetHelper::getGradeList($major->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td><strong><?php echo e($gr->name); ?></strong>
                        => <?php echo e($gr->mark_from.' - '.$gr->mark_to); ?>

                    </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </tr>
    </table>

</div>


<table style="width:100%; border-collapse:collapse; ">
    <tbody>
    <tr>
        <td><strong>NUMBER OF : </strong></td>
        <td><strong>Distinctions:</strong> <?php echo e(\App\Helpers\printMarkSheetHelper::countDistinctions($marks)); ?></td>
        <td><strong>Credits:</strong> <?php echo e(\App\Helpers\printMarkSheetHelper::countCredits($marks)); ?></td>
        <td><strong>Passes:</strong> <?php echo e(\App\Helpers\printMarkSheetHelper::countPasses($marks)); ?></td>
        <td><strong>Failures:</strong> <?php echo e(\App\Helpers\printMarkSheetHelper::countFailures($marks)); ?></td>
        <td><strong>Subjects Offered:</strong> <?php echo e(\App\Helpers\printMarkSheetHelper::countSubjectsOffered($marks)); ?></td>
    </tr>

    </tbody>
</table>
<?php /**PATH D:\Final-Project\resources\views/pages/support_team/marks/print/grading.blade.php ENDPATH**/ ?>