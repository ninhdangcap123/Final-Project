<table class="table table-bordered table-responsive text-center">
    <thead>
    <tr>
        <th rowspan="2">S/N</th>
        <th rowspan="2">SUBJECTS</th>
        <th rowspan="2">CA1<br>(20)</th>
        <th rowspan="2">CA2<br>(20)</th>
        <th rowspan="2">EXAMS<br>(60)</th>
        <th rowspan="2">TOTAL<br>(100)</th>

        

        <th rowspan="2">GRADE</th>
        <th rowspan="2">SUBJECT <br> POSITION</th>
        <th rowspan="2">REMARKS</th>
    </tr>
    </thead>

    <tbody>
    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($loop->iteration); ?></td>
            <td><?php echo e($sub->name); ?></td>
            <?php $__currentLoopData = $marks->where('subject_id', $sub->id)->where('exam_id', $ex->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td><?php echo e(($mk->t1) ?: '-'); ?></td>
                <td><?php echo e(($mk->t2) ?: '-'); ?></td>
                <td><?php echo e(($mk->exm) ?: '-'); ?></td>
                <td>
                    <?php if($ex->term === 1): ?> <?php echo e(($mk->tex1)); ?>

                    <?php elseif($ex->term === 2): ?> <?php echo e(($mk->tex2)); ?>

                    <?php elseif($ex->term === 3): ?> <?php echo e(($mk->tex3)); ?>

                    <?php else: ?> <?php echo e('-'); ?>

                    <?php endif; ?>
                </td>

                
                

                
                <td><?php echo e(($mk->grade) ? $mk->grade->name : '-'); ?></td>
                <td><?php echo ($mk->grade) ? \App\Helpers\printMarkSheetHelper::getSuffix($mk->sub_pos) : '-'; ?></td>
                <td><?php echo e(($mk->grade) ? $mk->grade->remark : '-'); ?></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td colspan="4"><strong>TOTAL SCORES OBTAINED: </strong> <?php echo e($exr->total); ?></td>
        <td colspan="3"><strong>FINAL AVERAGE: </strong> <?php echo e($exr->ave); ?></td>
        <td colspan="2"><strong>CLASS AVERAGE: </strong> <?php echo e($exr->class_ave); ?></td>
    </tr>
    </tbody>
</table>
<?php /**PATH D:\Final-Project\resources\views/pages/support_team/marks/show/sheet.blade.php ENDPATH**/ ?>