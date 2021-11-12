<form method="post" action="{{ route('students.promote_selector') }}">
    @csrf
    <div class="row">
        <div class="col-md-10">
            <fieldset>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fc" class="col-form-label font-weight-bold">From Course:</label>
                            <select required onchange="getClassSections(this.value, '#fs')" id="fc" name="fromCourse"
                                    class="form-control select">
                                <option value="">Select Courses</option>
                                @foreach($my_courses as $c)
                                    <option
                                        {{ ($selected && $fromCourse == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fs" class="col-form-label font-weight-bold">Class:</label>
                            <select required id="fs" name="fromSection" data-placeholder="Select Class First"
                                    class="form-control select">
                                @if($selected && $fromSection)
                                    <option value="{{ $fromSection }}">{{ $classes->where('id', $fromSection)->first()->name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tc" class="col-form-label font-weight-bold">To Course:</label>
                            <select required onchange="getClassSections(this.value, '#ts')" id="tc" name="toCourse"
                                    class="form-control select">
                                <option value="">Select Courses</option>
                                @foreach($my_courses as $c)
                                    <option
                                        {{ ($selected && $toCourse == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ts" class="col-form-label font-weight-bold">Class:</label>
                            <select required id="ts" name="toSection" data-placeholder="Select Class First"
                                    class="form-control select">
                                @if($selected && $toSection)
                                    <option value="{{ $toSection }}">{{ $classes->where('id', $toSection)->first()->name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                </div>

            </fieldset>
        </div>

        <div class="col-md-2 mt-4">
            <div class="text-right mt-1">
                <button type="submit" class="btn btn-primary">Manage Promotion <i class="icon-paperplane ml-2"></i>
                </button>
            </div>
        </div>

    </div>

</form>
