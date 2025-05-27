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

        @if($this->form->my_config['is_nested'])
            <div class="col-md-6 mb-3">
                <x-categories::form.select
                        name="form.parent_id"
                        :label="__('admix-categories::fields.parent_id')"
                        :model=\Agenciafmd\Articles\Models\Article::class
                />
            </div>
        @else
            <div class="col-md-6 mb-3">
                <!-- input here -->
            </div>
        @endif

        @if($this->form->my_config['has_description'])
            <div class="col-md-12 mb-3">
                <x-form.easymde
                        name="form.description"
                        :label="__('admix-categories::fields.description')"
                />
            </div>
        @endif

        @if($this->form->my_config['image'])
            <div class="col-md-12 mb-3">
                <x-form.image
                        name="form.image"
                        :label="__('admix-categories::fields.image')"
                        :hide-content="true"
                        :hide-crop="true"
                />
            </div>
        @endif

        <div class="col-md-6 mb-3">
            <x-form.number
                    name="form.sort"
                    :label="__('admix-categories::fields.sort')"
            />
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
