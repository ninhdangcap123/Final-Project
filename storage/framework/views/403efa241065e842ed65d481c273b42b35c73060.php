
<?php $__env->startSection('page_title', 'Manage Courses'); ?>
<?php $__env->startSection('content'); ?>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Courses</h6>
            <?php echo \App\Helpers\getSystemInfoHelper::getPanelOptions(); ?>

        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-classes" class="nav-link active" data-toggle="tab">Manage Courses</a></li>
                <li class="nav-item"><a href="#new-class" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Create New Course</a></li>
            </ul>

            <div class="tab-content">
                    <div class="tab-pane fade show active" id="all-classes">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Courses</th>
                                <th>Majors</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $my_classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($course->name); ?></td>
                                    <td><?php echo e($course->major->name); ?></td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    <?php if(\App\Helpers\checkUsersHelper::userIsTeamSA()): ?>
                                                    
                                                    <a href="<?php echo e(route('classes.edit', $course->id)); ?>" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                   <?php endif; ?>
                                                        <?php if(\App\Helpers\getUserTypeHelper::userIsSuperAdmin()): ?>
                                                    
                                                    <a id="<?php echo e($course->id); ?>" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                    <form method="post" id="item-delete-<?php echo e($course->id); ?>" action="<?php echo e(route('classes.destroy', $course->id)); ?>" class="hidden">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('delete'); ?>
                                                    </form>
                                                        <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                <div class="tab-pane fade" id="new-class">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span>When a course is created, a class will be automatically created for the course, you can edit it or add more classes to the course at <a target="_blank" href="<?php echo e(route('sections.index')); ?>">Manage Classes</a></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="<?php echo e(route('classes.store')); ?>">
                                <?php echo csrf_field(); ?>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="name" value="<?php echo e(old('name')); ?>" required type="text" class="form-control" placeholder="Name of Course">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="major_id" class="col-lg-3 col-form-label font-weight-semibold">Major</label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select Major" class="form-control select" name="major_id" id="major_id">
                                            <?php $__currentLoopData = $majors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $major): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option <?php echo e(old('major_id') == $major->id ? 'selected' : ''); ?> value="<?php echo e($major->id); ?>"><?php echo e($major->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button id="ajax-btn" type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Final-Project\resources\views/pages/support_team/classes/index.blade.php ENDPATH**/ ?>