
<li class="nav-item">
    <a href="<?php echo e(route('marks.year_select', \App\Helpers\displayMessageHelper::hash(Auth::user()->id))); ?>" class="nav-link <?php echo e(in_array(Route::currentRouteName(), ['marks.show', 'marks.year_selector', 'pins.enter']) ? 'active' : ''); ?>"><i class="icon-book"></i> Marksheet</a>
</li>
<?php /**PATH D:\Final-Project\resources\views/pages/student/menu.blade.php ENDPATH**/ ?>