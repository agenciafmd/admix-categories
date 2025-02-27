<x-page.form
        title="{{ $category->exists ? __('Update :name', ['name' => __(config('admix-categories.name'))]) : __('Create :name', ['name' => __(config('admix-categories.name'))]) }}">
    <div class="row">
        <div class="col-md-6 mb-3">
            <x-form.label
                    for="form.is_active">
                {{ str(__('admix-categories::fields.is_active'))->ucfirst() }}
            </x-form.label>
            <x-form.toggle
                    name="form.is_active"
                    :large="true"
                    :label-on="__('Yes')"
                    :label-off="__('No')"
            />
        </div>
        <div class="col-md-6 mb-3">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <x-form.input
                    name="form.name"
                    :label="__('admix-categories::fields.name')"
            />
        </div>
        <div class="col-md-6 mb-3">
            <!-- input here -->
        </div>
    </div>
    <x-slot:complement>
        @if($category->exists)
            <div class="mb-3">
                <x-form.plaintext
                        :label="__('admix::fields.id')"
                        :value="$category->id"
                />
            </div>
            <div class="mb-3">
                <x-form.plaintext
                        :label="__('admix::fields.slug')"
                        :value="$category->slug"
                />
            </div>
            <div class="mb-3">
                <x-form.plaintext
                        :label="__('admix::fields.created_at')"
                        :value="$category->created_at->format(config('admix.timestamp.format'))"
                />
            </div>
            <div class="mb-3">
                <x-form.plaintext
                        :label="__('admix::fields.updated_at')"
                        :value="$category->updated_at->format(config('admix.timestamp.format'))"
                />
            </div>
        @endif
    </x-slot:complement>
</x-page.form>
