
<?php $__env->startSection('page_title', 'Student Marksheet'); ?>
<?php $__env->startSection('content'); ?>

    <div class="card">
        <div class="card-header text-center">
            <h4 class="card-title font-weight-bold">Student Marksheet for =>  <?php echo e($sr->user->name.' ('.$my_class->name.' '.$my_class->section->first()->name.')'); ?> </h4>
        </div>
    </div>

    <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = $exam_records->where('exam_id', $ex->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <?php if(\App\Helpers\checkUsersHelper::userIsAcademic()): ?>
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h6 class="font-weight-bold"><?php echo e($ex->name.' - '.$ex->year); ?></h6>
                        <?php echo \App\Helpers\getSystemInfoHelper::getPanelOptions(); ?>

                    </div>

                    <div class="card-body collapse">

                        
                        <?php echo $__env->make('pages.support_team.marks.show.sheet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                        
                        <div class="text-center mt-3">
                            <a target="_blank" href="<?php echo e(route('marks.print', [\App\Helpers\displayMessageHelper::hash($student_id), $ex->id, $year])); ?>" class="btn btn-secondary btn-lg">Print Marksheet <i class="icon-printer ml-2"></i></a>
                        </div>

                    </div>

                </div>
            <?php endif; ?>

            
            <?php echo $__env->make('pages.support_team.marks.show.comments', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            
            <?php echo $__env->make('pages.support_team.marks.show.skills', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Final-Project\resources\views/pages/support_team/marks/show/index.blade.php ENDPATH**/ ?>