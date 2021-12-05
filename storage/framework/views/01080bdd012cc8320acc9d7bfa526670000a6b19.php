<?php if(\App\Helpers\checkUsersHelper::userIsTeamSAT()): ?>
    <div class="card">
        <div class="card-header header-elements-inline bg-dark">
            <h6 class="card-title font-weight-bold">Exam Comments</h6>
            <?php echo \App\Helpers\getSystemInfoHelper::getPanelOptions(); ?>

        </div>

        <div class="card-body collapse">
            <form class="ajax-update" method="post" action="<?php echo e(route('marks.comment_update', $exr->id)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <?php if(\App\Helpers\checkUsersHelper::userIsTeamSAT()): ?>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label font-weight-semibold">Teacher's Comment</label>
                        <div class="col-lg-10">
                            <input name="t_comment" value="<?php echo e($exr->t_comment); ?>"  type="text" class="form-control" placeholder="Teacher's Comment">
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(\App\Helpers\checkUsersHelper::userIsTeamSA()): ?>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label font-weight-semibold">Head Teacher's Comment</label>
                        <div class="col-lg-10">
                            <input name="p_comment" value="<?php echo e($exr->p_comment); ?>"  type="text" class="form-control" placeholder="Head Teacher's Comment">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\Final-Project\resources\views/pages/support_team/marks/show/comments.blade.php ENDPATH**/ ?>