@if (Route::is(['addons']))
    <!-- Add Addon -->
    <div class="modal fade" id="add_modifier">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Add Addon</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form method="POST" action="{{ route('addons.store') }}" id="form-add-addon">
                    @csrf
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label">Item <span class="text-danger">*</span></label>
                            <select class="form-select" name="item_id" required>
                                <option value="">Select</option>
                                @foreach(($items ?? collect()) as $it)
                                    <option value="{{ $it->id }}" {{ old('item_id') == $it->id ? 'selected' : '' }}>{{ $it->name }}</option>
                                @endforeach
                            </select>
                            @error('item_id')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Addon name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="addon_name" value="{{ old('addon_name') }}" placeholder="Addon name" required>
                            @error('addon_name')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="price" value="{{ old('price') }}" placeholder="0.00" required>
                            @error('price')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea rows="3" class="form-control" name="description" placeholder="Description">{{ old('description') }}</textarea>
                            @error('description')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Addon End -->

    <!-- Edit Addon -->
    <div class="modal fade" id="edit_modifier">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Edit Addon</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('addons')}}">
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3 d-flex align-items-center flex-wrap gap-3">
                            <div class="avatar avatar-3xl border bg-light">
                                <img src="{{URL::asset('build/img/items/food-02.jpg')}}" alt="addon" class="img-fluid">
                            </div>
                            <div>
                                <label class="form-label">Category Image<span class="text-danger"> *</span></label>
                                <p class="fs-13 mb-3">Image should be with in 5 MB</p>
                                <div class="d-flex align-items-center">
                                    <div class="btn btn-icon btn-sm btn-white rounded-circle position-relative me-2">
                                        <input type="file" class="form-control position-absolute w-100 h-100 top-0 start-0 opacity-0">
                                        <i class="icon-pencil-line"></i>
                                    </div>
                                    <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle text-danger"><i class="icon-trash-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Item<span class="text-danger"> *</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option selected>Pizza</option>
                                <option>Sea Food</option>
                                <option>Salad</option>
                                <option>Sauce</option>
                                <option>Topping</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Addon<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" value="Extra Cheese">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" value="$10">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description<span class="text-danger"> *</span></label>
                            <textarea rows="4" class="form-control">Extra cheese for your pizza.</textarea>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Addon End -->

    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('addons')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Start Filter -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filter-offcanvas">
        <div class="offcanvas-header pb-0">
            <div class="border-bottom d-flex align-items-center justify-content-between w-100 pb-3">
                <h4 class="offcanvas-title mb-0">Filter</h4>
                <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button>
            </div>
        </div>
        <div class="offcanvas-body d-flex flex-column pt-3">
            <div>
                <div class="mb-3">
                    <label class="form-label">Item<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Category</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Pizza
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Sea Food
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Salad
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Sauce
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Topping
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Addon<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Category</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Extra Cheese
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Grilled Shrimp
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Avocado Slices
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Spicy Mayo
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Crispy Bacon Bits
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Status<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Category</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Active
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Expired
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="d-flex align-items-center gap-2 mt-auto border-0">
                    <a href="#" class="btn btn-light w-100">Reset</a>
                    <a href="#" class="btn btn-primary w-100">Apply</a>
                </div>

        </div>
    </div>
    <!-- End Filter -->
@endif

@if (Route::is(['categories']))
    <!-- Add Category -->
    <div class="modal fade" id="add_category">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Add Category</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label">Category Name<span class="text-danger"> *</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Category -->
    <div class="modal fade" id="edit_category">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Edit Category</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form id="edit_category_form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label">Category Name<span class="text-danger"> *</span></label>
                            <input type="text" name="name" id="edit_category_name" class="form-control" required>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.querySelectorAll('.edit-category-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var name = this.dataset.name || '';
            document.getElementById('edit_category_form').action = '{{ url("categories") }}/' + id;
            document.getElementById('edit_category_name').value = name;
        });
    });
    </script>

    <!-- Start Filter -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filter-offcanvas">
        <div class="offcanvas-header pb-0">
            <div class="border-bottom d-flex align-items-center justify-content-between w-100 pb-3">
                <h4 class="offcanvas-title mb-0">Filter</h4>
                <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button>
            </div>
        </div>
        <div class="offcanvas-body d-flex flex-column pt-3">
            <div>
                <div class="mb-3">
                    <label class="form-label">Category<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Category</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Sea Food
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Pizza
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Salads
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Tacos
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Burgers
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status<span class="text-danger"> *</span></label>
                    <select class="select">
                        <option>Select</option>
                        <option>Active</option>
                        <option>Expired</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 mt-auto">
                <a href="#" class="btn btn-light w-100">Reset</a>
                <a href="#" class="btn btn-primary w-100">Apply</a>
            </div>
        </div>
    </div>
    <!-- End Filter -->
@endif

@if (Route::is(['coupons']))
    <!-- Show Coupons -->
    <div class="modal fade" id="show_coupon">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Coupon Code</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('coupons')}}">
                    <div class="modal-body p-4 pt-1">
                        <div class="p-4 bg-light border rounded border-dashed d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0" id="copytext">SEAFOOD10</h6>
                            <span class="copytoclipboard" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy to Clipboard"><i class="icon-copy fw-semibold "></i></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Show Coupons End -->

    <!-- Add Coupon -->
    <div class="modal fade" id="add_coupon">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Add Coupon</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form method="POST" action="{{ route('coupons.store') }}" id="form-add-coupon">
                    @csrf
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" value="{{ old('code') }}" placeholder="e.g. SAVE10" required>
                            @error('code')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valid Category</label>
                            <select class="form-select" name="category_id">
                                <option value="">All categories</option>
                                @foreach(($categories ?? collect()) as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                    <select class="form-select" name="discount_type" required>
                                        <option value="">Select</option>
                                        <option value="percentage" {{ old('discount_type', 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                    </select>
                                    @error('discount_type')<span class="text-danger small">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="discount_amount" value="{{ old('discount_amount') }}" placeholder="0.00" required>
                                    @error('discount_amount')<span class="text-danger small">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="valid_from" value="{{ old('valid_from') }}">
                                    @error('valid_from')<span class="text-danger small">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="date" class="form-control" name="valid_to" value="{{ old('valid_to') }}">
                                    @error('valid_to')<span class="text-danger small">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="is_active">
                                <option value="1" {{ (string) old('is_active', '1') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ (string) old('is_active', '1') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add coupons End -->

    <!-- Edit coupons -->
    <div class="modal fade" id="edit_coupon">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Edit Coupon</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('coupons')}}">
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label">Coupon Code<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" value="SEAFOOD10">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valid Category<span class="text-danger"> *</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option selected>Pizza</option>
                                <option>Sea Food</option>
                                <option>Salad</option>
                                <option>Sauce</option>
                                <option>Topping</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Type<span class="text-danger"> *</span></label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option selected>Percentage</option>
                                        <option>Fixed Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Amount<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" value="10%">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Start Date<span class="text-danger ms-1">*</span></label>
                                    <div class="input-group w-auto input-group-flat">
                                        <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d/m/Y" value="01 Jan 2026">
                                        <span class="input-group-text">
                                            <i class="icon-calendar-fold"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Expiry Date<span class="text-danger ms-1">*</span></label>
                                    <div class="input-group w-auto input-group-flat">
                                        <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d/m/Y" value="31 Jan 2026">
                                        <span class="input-group-text">
                                            <i class="icon-calendar-fold"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit coupons End -->

    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('coupons')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Start Filter -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filter-offcanvas">
        <div class="offcanvas-header pb-0">
            <div class="border-bottom d-flex align-items-center justify-content-between w-100 pb-3">
                <h4 class="offcanvas-title mb-0">Filter</h4>
                <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button>
            </div>
        </div>
        <div class="offcanvas-body d-flex flex-column pt-3">
            <div>
                <div class="mb-3">
                    <label class="form-label">Discount Type<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Category</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Percentage
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Fixed Amount
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Valid Category<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Category</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Sea Foods
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Pizza Orders
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Salads
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">All Categories
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Combo Meals
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date Range<span class="text-danger"> *</span></label>

                    <div class="daterangepick custom-date form-control w-auto d-flex align-items-center justify-content-between">
                        <span class="reportrange-picker"></span> <i class="icon-calendar-fold text-gray-5 fs-14 text-end"></i>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Status<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Category</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Active
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Expired
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="d-flex align-items-center gap-2 mt-auto border-0">
                    <a href="#" class="btn btn-light w-100">Reset</a>
                    <a href="#" class="btn btn-primary w-100">Apply</a>
                </div>

        </div>
    </div>
    <!-- End Filter -->
@endif

@if (Route::is(['customer', 'pos']))
    <!-- Add Customer -->
    <div class="modal fade" id="add_customer">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Add Customer</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{ route('customer.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3 d-flex align-items-center flex-wrap gap-3">
                            <div class="avatar avatar-3xl border bg-light">
                                <i class="icon-images fs-28 text-dark"></i>
                            </div>
                            <div>
                                <label class="form-label">Customer Image</label>
                                <p class="fs-13 mb-3">Image should be within 5 MB (optional)</p>
                                <div class="d-flex align-items-center">
                                    <div class="btn btn-icon btn-sm btn-white rounded-circle position-relative me-2">
                                        <input type="file" name="image" class="form-control position-absolute w-100 h-100 top-0 start-0 opacity-0" accept="image/*">
                                        <i class="icon-upload"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Customer Name<span class="text-danger"> *</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. +1 234 567 8900">
                            @error('phone')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="row algin-items-center justify-content-center">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <div class="input-group w-auto input-group-flat">
                                        <input type="text" name="date_of_birth" class="form-control" data-provider="flatpickr" data-date-format="Y-m-d" placeholder="yyyy-mm-dd" value="{{ old('date_of_birth') }}">
                                        <span class="input-group-text">
                                            <i class="icon-calendar-fold"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select" name="gender">
                                        <option value="">Select</option>
                                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Disabled" {{ old('status') === 'Disabled' ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Category End -->

    <!-- Edit Customer -->
    <div class="modal fade" id="edit_customer">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Edit Customer</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('customer')}}">
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3 d-flex align-items-center flex-wrap gap-3">
                            <div class="avatar avatar-3xl border bg-light">
                                <img src="{{URL::asset('build/img/profiles/avatar-32.jpg')}}" alt="category" class="img-fluid">
                            </div>
                            <div>
                                <label class="form-label">Customer Image<span class="text-danger"> *</span></label>
                                <p class="fs-13 mb-3">Image should be with in 5 MB</p>
                                <div class="d-flex align-items-center">
                                    <div class="btn btn-icon btn-sm btn-white rounded-circle position-relative me-2">
                                        <input type="file" class="form-control position-absolute w-100 h-100 top-0 start-0 opacity-0">
                                        <i class="icon-pencil-line"></i>
                                    </div>
                                    <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle text-danger"><i class="icon-trash-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Customer Name<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" value="Adrian James">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" value="+1 56985 65895">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email<span class="text-danger"> *</span></label>
                            <input type="mail" class="form-control" value="adrian@example.com">
                        </div>
                        <div class="row algin-items-center justify-content-center">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <div class="input-group w-auto input-group-flat">
                                        <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="dd/mm/yyyy" value="25/12/2026">
                                        <span class="input-group-text">
                                            <i class="icon-calendar-fold"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option selected>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="select">
                                <option>Select</option>
                                <option selected>Active</option>
                                <option>Disabled</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Category End -->

    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('customer')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Start Filter -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filter-offcanvas">
        <div class="offcanvas-header pb-0">
            <div class="border-bottom d-flex align-items-center justify-content-between w-100 pb-3">
                <h4 class="offcanvas-title mb-0">Filter</h4>
                <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button>
            </div>
        </div>
        <div class="offcanvas-body d-flex flex-column pt-3">
            <div>
                <div class="mb-3">
                    <label class="form-label">Category<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Category</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Sea Food
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Pizza
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Salads
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Tacos
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Burgers
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status<span class="text-danger"> *</span></label>
                    <select class="select">
                        <option>Select</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 mt-auto">
                <a href="#" class="btn btn-light w-100">Reset</a>
                <a href="#" class="btn btn-primary w-100">Apply</a>
            </div>
        </div>
    </div>
    <!-- End Filter -->

    <!-- Customer Details -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="view_details">
        <div class="offcanvas-header d-flex align-items-center justify-content-between border-bottom">
            <h4 class="offcanvas-title mb-0">Customer Details</h4>
            <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button>
        </div>
        <form action="{{url('customer')}}" class="d-flex flex-column overflow-y-auto h-100">
            <div class="offcanvas-body p-0">
                <div class="border-bottom p-4">
                <div class="mb-4 d-flex align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-xxl border me-3 flex-shrink-0">
                            <img src="{{URL::asset('build/img/profiles/avatar-09.jpg')}}" alt="customer" class="img-fluid">
                        </span>
                        <div>
                            <h6 class="fs-14 fw-semibold mb-0">David Belcher</h6>
                            <p class="fs-13 mb-0">Last Login : 25 Jan 2026</p>
                        </div>
                    </div>
                    <span class="badge badge-soft-success">Active</span>
                </div>
                <div>
                    <div class="d-sm-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                        <span class="d-flex align-items-center"><i class="icon-mail text-dark me-1"></i>Email</span>
                        <span class="text-dark">david@example.com</span>
                    </div>
                    <div class="d-sm-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                        <span class="d-flex align-items-center"><i class="icon-phone text-dark me-1"></i>Phone</span>
                        <span class="text-dark">+1 14524 54595</span>
                    </div>
                    <div class="d-sm-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                        <span class="d-flex align-items-center"><i class="icon-map-pin-check-inside text-dark me-1"></i>Address</span>
                        <span class="text-wrap-1 text-dark">301 Market Street, Riverside Drive, FL 33128, USA</span>
                    </div>
                    <div class="d-sm-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <span class="d-flex align-items-center"><i class="icon-user text-dark me-1"></i>Gender</span>
                        <span class="text-dark">Male</span>
                    </div>
                </div>
                </div>
                <div class="p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5>Orders</h5>
                        <span class="badge bg-light text-dark fw-semibold">Orders : 02</span>
                    </div>
                    <div class="border p-3 rounded mb-3">
                        <div class="row align-items-center border-bottom flex-wrap g-3 pb-3 mb-3">
                            <div class="col-sm-4">
                                <h6 class="fs-14 fw-semibold mb-1">#56998</h6>
                                <span>15 Sep 2026</span>
                            </div>
                            <div class="col-sm-4">
                                <p class="mb-1">Order total</p>
                                <span class="text-dark fw-medium">$350</span>
                            </div>
                            <div class="col-sm-4 text-sm-end">
                                <span class="badge badge-soft-orange">Pending</span>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <p class="mb-1">Order Type</p>
                                <span class="text-dark fw-medium">Dine-In (T4)</span>
                            </div>
                            <div class="col-sm-4">
                                <p class="mb-1">No of Guests</p>
                                <span class="text-dark fw-medium">4</span>
                            </div>
                            <div class="col-sm-4">
                                <p class="mb-1">No of Items</p>
                                <span class="text-dark fw-medium">8</span>
                            </div>
                        </div>
                    </div>
                    <div class="border p-3 rounded">
                        <div class="row align-items-center border-bottom flex-wrap g-3 pb-3 mb-3">
                            <div class="col-sm-4">
                                <h6 class="fs-14 fw-semibold mb-1">#25654</h6>
                                <span>21 Sep 2026</span>
                            </div>
                            <div class="col-sm-4">
                                <p class="mb-1">Order total</p>
                                <span class="text-dark fw-medium">$620</span>
                            </div>
                            <div class="col-sm-4 text-sm-end">
                                <span class="badge badge-soft-success">Completed</span>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <p class="mb-1">Order Type</p>
                                <span class="text-dark fw-medium">Take Away</span>
                            </div>
                            <div class="col-sm-4">
                                <p class="mb-1">No of Guests</p>
                                <span class="text-dark fw-medium">8</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="offcanvas-footer d-flex align-items-center gap-2 pt-2 border-0">
                <button type="button" class="btn btn-dark d-flex align-items-center w-100" data-bs-toggle="modal" data-bs-target="#edit_customer"><i class="icon-pencil-line me-1"></i>Edit Customer</button>
            </div>
        </form>
    </div>
    <!-- End Customer Details -->
@endif

@if (Route::is(['invoices']))
    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('invoices')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Start Filter -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filter-offcanvas">
        <div class="offcanvas-header pb-0">
            <div class="border-bottom d-flex align-items-center justify-content-between w-100 pb-3">
                <h4 class="offcanvas-title mb-0">Filter</h4>
                <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button>
            </div>
        </div>
        <div class="offcanvas-body d-flex flex-column pt-3">
            <div>
                <div class="mb-3">
                    <label class="form-label">Customer<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Customer</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Adrian James
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Sue Allen
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Frank Barrett
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Kelley Davis
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Jim Vickers
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Order Type<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Order Type</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Dine In
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Take Away
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Delivery
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date Range<span class="text-danger"> *</span></label>
                    <div class="daterangepick custom-date form-control w-auto d-flex align-items-center justify-content-between">
                        <span class="reportrange-picker"></span> <i class="icon-calendar-fold text-gray-5 fs-14 text-end"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status<span class="text-danger"> *</span></label>
                    <select class="select">
                        <option>Select</option>
                        <option>Paid</option>
                        <option>Unpaid</option>
                    </select>
                </div>
            </div>

                <div class="d-flex align-items-center gap-2 mt-auto border-0">
                    <a href="#" class="btn btn-light w-100">Reset</a>
                    <a href="#" class="btn btn-primary w-100">Apply</a>
                </div>

        </div>
    </div>
    <!-- End Filter -->
@endif

@if (Route::is(['items']))
    <!-- Items Details (filled by JS from clicked item card) -->
    <div class="modal fade" id="items_details">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-4 pb-0 border-0 mb-3">
                    <div class="border-bottom d-flex align-items-center justify-content-between flex-fill pb-3">
                        <h4 class="modal-title">Item Details</h4>
                        <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                    </div>
                </div>
                <div class="modal-body p-4 pt-1">
                    <div class="row row-gap-3">
                        <div class="col-lg-6 col-sm-12">
                            <div class="bg-light p-3 rounded">
                                <img id="items-details-img" src="" alt="" class="img-fluid w-100 rounded" style="max-height:280px;object-fit:cover;">
                                <div id="items-details-img-placeholder" class="d-none d-flex align-items-center justify-content-center bg-light rounded" style="height:200px"><i class="icon-layout-list fs-48 text-muted"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="mb-3">
                                <h5 id="items-details-title">–</h5>
                                <p id="items-details-desc" class="text-muted small mb-0">–</p>
                            </div>
                            <div class="mb-3 pb-3 border-bottom" id="items-details-sizes-wrap">
                                <h6>Sizes</h6>
                                <div class="d-flex flex-wrap gap-2" id="items-details-sizes"></div>
                            </div>
                            <div>
                                <h6 class="mb-3">Add-ons & Upgrades</h6>
                                <div class="row g-2" id="items-details-addons"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    (function() {
        function decodeHtmlEntities(str) {
                if (!str || typeof str !== "string") return "";
                var txt = document.createElement("textarea");
                txt.innerHTML = str;
                return txt.value;
            }
        function fillItemDetailsModal() {
            var d = window._itemDetailsData;
            if (!d) return;
            document.getElementById("items-details-title").textContent = d.name || "–";
            document.getElementById("items-details-desc").textContent = decodeHtmlEntities(d.description || "") || "No description.";
            var imgEl = document.getElementById("items-details-img");
            var placeholder = document.getElementById("items-details-img-placeholder");
            if (d.image) {
                imgEl.src = d.image;
                imgEl.alt = d.name || "";
                imgEl.classList.remove("d-none");
                if (placeholder) placeholder.classList.add("d-none");
            } else {
                imgEl.classList.add("d-none");
                if (placeholder) placeholder.classList.remove("d-none");
            }
            var sizesWrap = document.getElementById("items-details-sizes-wrap");
            var sizesEl = document.getElementById("items-details-sizes");
            var addonsEl = document.getElementById("items-details-addons");
            if (sizesEl) {
                sizesEl.innerHTML = "";
                try {
                    var variationsStr = typeof d.variations === "string" ? (d.variations || "").replace(/&quot;/g, '"') : "";
                    var variations = variationsStr ? JSON.parse(variationsStr) : (Array.isArray(d.variations) ? d.variations : []);
                    if (!Array.isArray(variations)) variations = [];
                    if (variations.length) {
                        if (sizesWrap) sizesWrap.style.display = "";
                        variations.forEach(function(v) {
                            var span = document.createElement("span");
                            span.className = "badge bg-light text-dark border px-3 py-2";
                            var p = v.price != null && v.price !== "" ? parseFloat(v.price) : 0;
                            span.textContent = (v.name || "") + (isNaN(p) ? "" : " $" + p.toFixed(2));
                            sizesEl.appendChild(span);
                        });
                    } else {
                        if (sizesWrap) sizesWrap.style.display = "none";
                    }
                } catch (e) {
                    if (sizesWrap) sizesWrap.style.display = "none";
                }
            }
            if (addonsEl) {
                addonsEl.innerHTML = "";
                try {
                    var addonsStr = typeof d.addons === "string" ? (d.addons || "").replace(/&quot;/g, '"') : "";
                    var addons = addonsStr ? JSON.parse(addonsStr) : (Array.isArray(d.addons) ? d.addons : []);
                    if (!Array.isArray(addons)) addons = [];
                    if (addons.length) {
                        addons.forEach(function(a) {
                            var col = document.createElement("div");
                            col.className = "col-lg-6";
                            var p = a.price != null && a.price !== "" ? parseFloat(a.price) : 0;
                            col.innerHTML = '<div class="border p-2 rounded"><div class="d-flex align-items-center gap-2"><span class="avatar rounded-circle border bg-light flex-shrink-0" style="width:40px;height:40px;"></span><h6 class="fs-12 fw-medium mb-0">' + (a.addon_name || a.name || "").replace(/</g, "&lt;") + '</h6><span class="ms-auto small">$' + (isNaN(p) ? "0.00" : p.toFixed(2)) + '</span></div></div>';
                            addonsEl.appendChild(col);
                        });
                    } else {
                        addonsEl.innerHTML = '<p class="small text-muted mb-0">No add-ons</p>';
                    }
                } catch (e) {
                    addonsEl.innerHTML = '<p class="small text-muted mb-0">No add-ons</p>';
                }
            }
        }
        document.addEventListener("click", function(e) {
            if (e.target.closest && e.target.closest(".item-details-trigger")) {
                e.preventDefault();
                var card = e.target.closest(".item-card");
                if (!card) return;
                var getData = function(name) { return card.getAttribute("data-item-" + name) || ""; };
                window._itemDetailsData = {
                    name: getData("name"),
                    description: getData("description"),
                    image: getData("image"),
                    variations: getData("variations"),
                    addons: getData("addons")
                };
                var sizesWrap = document.getElementById("items-details-sizes-wrap");
                if (sizesWrap) sizesWrap.style.display = "";
                fillItemDetailsModal();
                var modalEl = document.getElementById("items_details");
                if (modalEl && typeof bootstrap !== "undefined") bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });
        document.getElementById("items_details") && document.getElementById("items_details").addEventListener("show.bs.modal", function() {
            if (window._itemDetailsData) fillItemDetailsModal();
        });
    })();
    </script>
    <!-- Items Details End -->

    <!-- Add Item (theme: image, name, description, price, net price, category, tax, variations, addons) -->
    <div class="modal fade" id="add_item">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 p-4">
                    <div class="d-flex align-items-center justify-content-between flex-fill pb-3 border-bottom">
                        <h4 class="modal-title">Add Item</h4>
                        <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                    </div>
                </div>
                <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 pt-1">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Item Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <p class="small text-muted mb-0 mt-1">Image should be within 5 MB</p>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Item Name<span class="text-danger"> *</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="4" class="form-control" placeholder="Item description (optional)">{{ old('description') }}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Price<span class="text-danger"> *</span></label>
                                    <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ old('price') }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Net Price</label>
                                    <input type="number" name="net_price" class="form-control" step="0.01" min="0" value="{{ old('net_price') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Category<span class="text-danger"> *</span></label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach($categories ?? collect() as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @if(($categories ?? collect())->isEmpty())
                                        <p class="small text-muted mt-1 mb-0">No categories yet. <a href="{{ route('categories') }}">Add a category</a> first.</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Tax</label>
                                    <select name="tax_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($taxes ?? collect() as $tax)
                                            <option value="{{ $tax->id }}" {{ old('tax_id') == $tax->id ? 'selected' : '' }}>{{ $tax->name }}({{ number_format($tax->rate, 0) }}%)</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Food Type</label>
                                    <select name="food_type" class="form-select">
                                        <option value="veg" {{ old('food_type', 'veg') == 'veg' ? 'selected' : '' }}>Veg</option>
                                        <option value="non_veg" {{ old('food_type') == 'non_veg' ? 'selected' : '' }}>Non Veg</option>
                                        <option value="egg" {{ old('food_type') == 'egg' ? 'selected' : '' }}>Egg</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <h6 class="mb-2">Variations</h6>
                                <p class="small text-muted mb-2">Size and price (optional)</p>
                                <div id="add-item-variations">
                                    <div class="row g-2 mb-2 variation-row">
                                        <div class="col-5"><input type="text" name="variations[name][]" class="form-control form-control-sm" placeholder="Size"></div>
                                        <div class="col-4"><input type="number" name="variations[price][]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price"></div>
                                        <div class="col-3"><button type="button" class="btn btn-sm btn-outline-primary btn-add-variation">Add</button></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <h6 class="mb-2">Add Ons</h6>
                                <p class="small text-muted mb-2">Name and price (optional)</p>
                                <div id="add-item-addons">
                                    <div class="row g-2 mb-2 addon-row">
                                        <div class="col-5"><input type="text" name="addons[name][]" class="form-control form-control-sm" placeholder="Name"></div>
                                        <div class="col-4"><input type="number" name="addons[price][]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price($)"></div>
                                        <div class="col-3"><button type="button" class="btn btn-sm btn-outline-primary btn-add-addon">Add</button></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" @if(($categories ?? collect())->isEmpty()) disabled @endif>Save Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Item End -->

    <!-- Edit Item (same fields as Add + prefill variations/addons) -->
    <div class="modal fade" id="edit_item">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 p-4">
                    <div class="d-flex align-items-center justify-content-between flex-fill pb-3 border-bottom">
                        <h4 class="modal-title">Edit Item</h4>
                        <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                    </div>
                </div>
                <form id="edit_item_form" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 pt-1">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Item Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" id="edit_item_image">
                                    <p class="small text-muted mb-0 mt-1">Image should be within 5 MB. Leave empty to keep current.</p>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Item Name<span class="text-danger"> *</span></label>
                                    <input type="text" name="name" id="edit_item_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="edit_item_description" rows="4" class="form-control" placeholder="Item description (optional)"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Price<span class="text-danger"> *</span></label>
                                    <input type="number" name="price" id="edit_item_price" class="form-control" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Net Price</label>
                                    <input type="number" name="net_price" id="edit_item_net_price" class="form-control" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Category<span class="text-danger"> *</span></label>
                                    <select name="category_id" id="edit_item_category_id" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach($categories ?? collect() as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Tax</label>
                                    <select name="tax_id" id="edit_item_tax_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($taxes ?? collect() as $tax)
                                            <option value="{{ $tax->id }}">{{ $tax->name }}({{ number_format($tax->rate, 0) }}%)</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Food Type</label>
                                    <select name="food_type" id="edit_item_food_type" class="form-select">
                                        <option value="veg">Veg</option>
                                        <option value="non_veg">Non Veg</option>
                                        <option value="egg">Egg</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <h6 class="mb-2">Variations</h6>
                                <div id="edit-item-variations"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-1 btn-add-edit-variation">Add</button>
                            </div>
                            <div class="col-12">
                                <h6 class="mb-2">Add Ons</h6>
                                <div id="edit-item-addons"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-1 btn-add-edit-addon">Add</button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" @if(($categories ?? collect())->isEmpty()) disabled @endif>Save Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    (function() {
        var editItemForm = document.getElementById('edit_item_form');
        if (!editItemForm) return;
        var editVariationsContainer = document.getElementById('edit-item-variations');
        var editAddonsContainer = document.getElementById('edit-item-addons');
        function parseJsonAttr(str) {
            if (!str || typeof str !== 'string') return null;
            str = str.trim();
            if (!str || str === '[]') return [];
            str = str.replace(/&quot;/g, '"').replace(/&#34;/g, '"');
            try {
                var out = JSON.parse(str);
                return Array.isArray(out) ? out : [];
            } catch (e) {
                return [];
            }
        }
        function editVariationRow(name, price, idx) {
            if (idx === undefined) idx = editVariationsContainer.querySelectorAll('.row').length;
            var row = document.createElement('div');
            row.className = 'row g-2 mb-2 align-items-center';
            var nameVal = String(name || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            var priceNum = price !== undefined && price !== null && price !== '' ? parseFloat(price) : '';
            var priceVal = priceNum === '' || isNaN(priceNum) ? '' : priceNum;
            row.innerHTML = '<div class="col-4"><input type="text" name="variations[' + idx + '][name]" class="form-control form-control-sm" placeholder="Size" value="' + nameVal + '"></div><div class="col-3"><input type="number" name="variations[' + idx + '][price]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price" value="' + priceVal + '"></div><div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-edit-row">Remove</button></div>';
            editVariationsContainer.appendChild(row);
        }
        function editAddonRow(name, price, idx) {
            if (idx === undefined) idx = editAddonsContainer.querySelectorAll('.row').length;
            var row = document.createElement('div');
            row.className = 'row g-2 mb-2 align-items-center';
            var nameVal = String(name || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            var priceNum = price !== undefined && price !== null && price !== '' ? parseFloat(price) : '';
            var priceVal = priceNum === '' || isNaN(priceNum) ? '' : priceNum;
            row.innerHTML = '<div class="col-4"><input type="text" name="addons[' + idx + '][name]" class="form-control form-control-sm" placeholder="Name" value="' + nameVal + '"></div><div class="col-3"><input type="number" name="addons[' + idx + '][price]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price($)" value="' + priceVal + '"></div><div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-edit-row">Remove</button></div>';
            editAddonsContainer.appendChild(row);
        }
        function reindexEditRows() {
            [].forEach.call(editVariationsContainer.querySelectorAll('.row'), function(r, i) {
                var inputs = r.querySelectorAll('input');
                if (inputs[0]) inputs[0].name = 'variations[' + i + '][name]';
                if (inputs[1]) inputs[1].name = 'variations[' + i + '][price]';
            });
            [].forEach.call(editAddonsContainer.querySelectorAll('.row'), function(r, i) {
                var inputs = r.querySelectorAll('input');
                if (inputs[0]) inputs[0].name = 'addons[' + i + '][name]';
                if (inputs[1]) inputs[1].name = 'addons[' + i + '][price]';
            });
        }
        document.addEventListener('click', function(e) {
            var btn = e.target && e.target.closest && e.target.closest('.edit-item-btn');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();
            var card = btn.closest && btn.closest('.item-card');
            if (!card) return;
            var id = card.getAttribute('data-item-id');
            if (!id) return;
            editItemForm.action = '{{ url("items") }}/' + id;
            document.getElementById('edit_item_name').value = card.getAttribute('data-item-name') || '';
            document.getElementById('edit_item_description').value = card.getAttribute('data-item-description') || '';
            document.getElementById('edit_item_price').value = card.getAttribute('data-item-price') || '';
            var netPrice = card.getAttribute('data-item-net-price');
            document.getElementById('edit_item_net_price').value = netPrice !== null && netPrice !== '' ? netPrice : '';
            document.getElementById('edit_item_food_type').value = card.getAttribute('data-item-food-type') || 'veg';
            document.getElementById('edit_item_category_id').value = card.getAttribute('data-item-category-id') || '';
            document.getElementById('edit_item_tax_id').value = card.getAttribute('data-item-tax-id') || '';
            document.getElementById('edit_item_image').value = '';
            editVariationsContainer.innerHTML = '';
            editAddonsContainer.innerHTML = '';
            var variationsStr = card.getAttribute('data-item-variations');
            var variations = parseJsonAttr(variationsStr) || [];
            if (!Array.isArray(variations)) variations = [];
            variations.forEach(function(v) {
                editVariationRow(v.name, v.price);
            });
            if (variations.length === 0) editVariationRow('', '');
            var addonsStr = card.getAttribute('data-item-addons');
            var addons = parseJsonAttr(addonsStr) || [];
            if (!Array.isArray(addons)) addons = [];
            addons.forEach(function(a) {
                editAddonRow(a.addon_name || a.name, a.price);
            });
            if (addons.length === 0) editAddonRow('', '');
            var modalEl = document.getElementById('edit_item');
            if (modalEl && typeof bootstrap !== 'undefined') bootstrap.Modal.getOrCreateInstance(modalEl).show();
        });
        if (editAddonsContainer) editAddonsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-edit-row')) { var r = e.target.closest('.row'); if (r) r.remove(); reindexEditRows(); }
        });
        if (editVariationsContainer) editVariationsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-edit-row')) { var r = e.target.closest('.row'); if (r) r.remove(); reindexEditRows(); }
        });
        if (document.querySelector('.btn-add-edit-variation')) document.querySelector('.btn-add-edit-variation').addEventListener('click', function() { editVariationRow('', ''); });
        if (document.querySelector('.btn-add-edit-addon')) document.querySelector('.btn-add-edit-addon').addEventListener('click', function() { editAddonRow('', ''); });
        if (editItemForm) editItemForm.addEventListener('submit', function(e) { reindexEditRows(); });
    })();
    document.getElementById('add_item') && document.getElementById('add_item').addEventListener('show.bs.modal', function() {
        var v = document.getElementById('add-item-variations');
        var a = document.getElementById('add-item-addons');
        if (v) v.querySelectorAll('.variation-row').forEach(function(r, i) { if (i > 0) r.remove(); });
        if (a) a.querySelectorAll('.addon-row').forEach(function(r, i) { if (i > 0) r.remove(); });
    });
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-add-variation')) {
            var t = e.target.closest('#add-item-variations');
            if (t) {
                var row = document.createElement('div');
                row.className = 'row g-2 mb-2 variation-row';
                row.innerHTML = '<div class="col-5"><input type="text" name="variations[name][]" class="form-control form-control-sm" placeholder="Size"></div><div class="col-4"><input type="number" name="variations[price][]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price"></div><div class="col-3"><button type="button" class="btn btn-sm btn-outline-primary btn-add-variation">Add</button></div>';
                t.appendChild(row);
            }
        }
        if (e.target.classList.contains('btn-add-addon')) {
            var t = e.target.closest('#add-item-addons');
            if (t) {
                var row = document.createElement('div');
                row.className = 'row g-2 mb-2 addon-row';
                row.innerHTML = '<div class="col-5"><input type="text" name="addons[name][]" class="form-control form-control-sm" placeholder="Name"></div><div class="col-4"><input type="number" name="addons[price][]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price($)"></div><div class="col-3"><button type="button" class="btn btn-sm btn-outline-primary btn-add-addon">Add</button></div>';
                t.appendChild(row);
            }
        }
    });
    </script>
    <!-- Start Modal  -->
    <div class="modal fade" id="delete_item">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('items')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Hide Item modal: form action set by JS when opening -->
    <div class="modal fade" id="hide_item">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form id="hide_item_form" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body text-center p-4">
                        <div class="mb-4">
                            <span class="avatar avatar-xxl rounded-circle bg-info-subtle">
                                <img src="{{URL::asset('build/img/icons/hide.svg')}}" alt="hide" class="img-fluid w-auto h-auto">
                            </span>
                        </div>
                        <h4 class="mb-1">Hide Item Confirmation</h4>
                        <p class="mb-4">Are you sure you want to hide?</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-info w-100">Hide</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.querySelectorAll('.hide-item-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = this.dataset.id;
            var form = document.getElementById('hide_item_form');
            if (form) form.action = '{{ url("items") }}/' + id + '/hide';
        });
    });
    </script>
    <!-- End Modal  -->
@endif

@if (Route::is(['kanban-view']))
    <!-- Print details modal -->
    <div class="modal fade" id="print_reciept">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Print Reciept</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body">
                    <!-- Item 1 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h5 class="mb-3 fs-16">Order Info</h5>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Date & Time <span class="fw-medium text-dark">25/11/2026 - 08:45 PM</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Order No <span class="fw-medium text-dark"> #54654</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Token No <span class="fw-medium text-dark"> 20</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> No of Items <span class="fw-medium text-dark"> 4</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Order Type<span class="fw-medium text-dark"> Dine In (TabIe 4) </span> </h6>
                    </div>

                    <!-- Item 2 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h5 class="mb-3 fs-16">Ordered Menus</h5>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Grilled Chicken ×1 <span class="fw-medium text-dark">$49</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Chicken Taco ×2 <span class="fw-medium text-dark"> $66</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Lobster Thermidor ×1 <span class="fw-medium text-dark"> $76</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Grilled Chicken ×1 <span class="fw-medium text-dark"> $62</span> </h6>
                    </div>

                    <!-- Item 3 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Sub Total<span class="fw-medium text-dark">$267</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Tax (10%)<span class="fw-medium text-dark"> $268</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Service Charge <span class="fw-medium text-dark"> $15</span> </h6>
                    </div>
                    <h5 class="mb-0 d-flex align-items-center justify-content-between">Total <span>$274</span></h5>
                </div>

                <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                    <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary w-100 m-0">Print</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pay & Complete Order modal -->
    <div class="modal fade" id="pay_complete_order">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Pay & Complete Order</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 border rounded mb-4">
                        <h3 class="text-center mb-0">Final Total : $274</h3>
                    </div>
                    <!-- start row -->
                    <div class="row g-4">
                        <div class="col-lg-6 border-end">
                            <!-- Item 1 -->
                            <div class="mb-3 pb-3 border-bottom">
                                <h5 class="mb-3 fs-16">Order Info</h5>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Order No <span class="fw-medium text-dark"> #54654</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> No of Items <span class="fw-medium text-dark"> 4</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Order Type<span class="fw-medium text-dark"> Dine In (TabIe 4) </span> </h6>
                            </div>

                            <!-- Item 2 -->
                            <div class="mb-3 pb-3 border-bottom orders-list">
                                <h5 class="mb-3 fs-16">Ordered Menus</h5>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 orders-two">Grilled Chicken ×1 <span class="line"></span><span class="fw-medium text-dark">$49</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 orders-two"> Chicken Taco ×2 <span class="line"></span><span class="fw-medium text-dark"> $66</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 orders-two"> Lobster Thermidor ×1 <span class="line"></span><span class="fw-medium text-dark"> $76</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0 orders-two"> Grilled Chicken ×1 <span class="line"></span><span class="fw-medium text-dark"> $62</span> </h6>
                            </div>

                            <!-- Item 3 -->
                            <div>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Sub Total<span class="fw-medium text-dark">$267</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Tax (10%)<span class="fw-medium text-dark"> $268</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Discount (15%)<span class="fw-medium text-dark"> $26.7</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Service Charge <span class="fw-medium text-dark"> $15</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Coupon (FIRSTORDER) <span class="fw-medium text-danger"> -$45</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Tip <span class="fw-medium text-dark"> $20</span> </h6>
                            </div>

                        </div>
                        <!-- end cold -->
                        <div class="col-lg-6">
                            <div>
                                <h6 class="mb-3">Payment Type</h6>
                                <ul class="nav nav-tabs nav-tabs-solid border-0 flex-nowrap mb-4" role="tablist">
                                    <li class="nav-item w-100">
                                        <a href="#order-tab6" class="nav-link active d-flex align-items-center justify-content-center w-100" data-bs-toggle="tab"><i class="icon-dollar-sign me-1"></i>Cash</a>
                                    </li>
                                    <li class="nav-item w-100">
                                        <a href="#order-tab7" class="nav-link d-flex align-items-center justify-content-center w-100" data-bs-toggle="tab"><i class="icon-credit-card me-1"></i>Card</a>
                                    </li>
                                    <li class="nav-item w-100">
                                        <a href="#order-tab8" class="nav-link d-flex align-items-center justify-content-center w-100" data-bs-toggle="tab"><i class="icon-scan-text me-1"></i>Scan</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Item 1 -->
                                    <div class="tab-pane show active" id="order-tab6">
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Discount <span class="line"></span> <a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#calculator"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Tips <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_tips_calci"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Coupon <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Given Amount<span class="text-danger"> *</span></label>
                                                    <input type="text" class="form-control" value="300">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Balance<span class="text-danger"> *</span></label>
                                                    <input type="text" class="form-control" value="26">
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold">Note</label>
                                            <textarea rows="4" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <!-- Item 2 -->
                                    <div class="tab-pane" id="order-tab7">
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Discount <span class="line"></span> <a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#calculator"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Tips <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_tips_calci"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Coupon <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="mb-4 text-center p-4 border rounded d-flex align-items-center justify-content-center flex-column gap-2">
                                            <img src="{{URL::asset('build/img/icons/mobile-phone.svg')}}" alt="Mobile" class="img-fluid d-block custom-line-img-two">
                                            Tap or Swipe your card to pay
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold">Note</label>
                                            <textarea rows="4" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <!-- Item 3 -->
                                    <div class="tab-pane" id="order-tab8">
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Discount <span class="line"></span> <a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#calculator"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Tips <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_tips_calci"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Coupon <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="mb-4 text-center p-4 border rounded d-flex align-items-center justify-content-center flex-column gap-2">
                                            <img src="{{URL::asset('build/img/icons/qr-img.svg')}}" alt="Mobile" class="img-fluid d-block custom-line-img-two">
                                            Scan with your UPI app to pay
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold">Note</label>
                                            <textarea rows="4" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>

                <div class="modal-footer d-flex align-items-center justify-content-end flex-nowrap gap-2">
                    <button type="button" class="btn btn-light m-0" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary m-0">Pay & Complete Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- start offcanvas -->
    <div class="offcanvas offcanvas-offset offcanvas-end" tabindex="-1"  id="view_details">
        <div class="offcanvas-header d-block border-bottom">
            <div class="d-flex align-items-center justify-content-between">
            <h4 class="title mb-0">Order : #22154</h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button></div>
        </div>
        <div class="offcanvas-body">

            <!-- Item 1 -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                        <h6 class="mb-0">Order Status</h6>
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-1"><i class="icon-clock fs-14"></i> 06:24 PM</h6>
                    </div>
                    <div class="orders-list-two">
                        <!-- start row -->
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="status-item text-center">
                                    <div class="avatar bg-primary rounded-circle mb-2">
                                        <i class="icon-check"></i>
                                    </div>
                                    <p class="mb-0">Accepted</p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="status-item text-center">
                                    <div class="avatar bg-warning rounded-circle mb-2">
                                        <i class="icon-cooking-pot"></i>
                                    </div>
                                    <p class="mb-0">In Kitchen</p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="status-item text-center">
                                    <div class="avatar bg-light rounded-circle mb-2 text-dark">
                                        <i class="icon-flag"></i>
                                    </div>
                                    <p class="mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="mb-4 pb-4 border-bottom">
                <h5 class="mb-3 fs-16">Order Info</h5>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 text-body">Date & Time <span class="fw-medium text-dark">25/11/2026 - 08:45 PM</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 text-body"> Order No <span class="fw-medium text-dark"> #54654</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 text-body"> Token No <span class="fw-medium text-dark"> 20</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 text-body"> No of Items <span class="fw-medium text-dark"> 4</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0 text-body"> Order Type<span class="fw-medium text-dark"> Dine In (TabIe 4) </span> </h6>
            </div>

            <!-- Item 3 -->
            <div class="mb-4 pb-4 border-bottom orders-list-three">
                <h5 class="mb-3 fs-16">Items</h5>
                <div class="status-item mb-4 d-flex align-items-center justify-content-start gap-2">
                    <div class="avatar bg-success rounded-circle flex-shrink-0">
                        <i class="icon-flag"></i>
                    </div>
                    <div>
                        <p class="d-flex align-items-center gap-2 mb-2 text-dark fw-medium">Margherita Pizza <span>x1</span></p>
                        <div class="bg-light rounded py-1 px-2">
                            <p class="mb-0 fw-medium d-flex align-items-center text-dark"> <i class="icon-badge-info me-1"></i> Notes : Extra Spicy,  With extra Pepperoni</p>
                        </div>
                    </div>
                </div>

                <div class="status-item mb-4 d-flex align-items-center justify-content-start gap-2">
                    <div class="avatar bg-warning rounded-circle flex-shrink-0">
                        <i class="icon-cooking-pot"></i>
                    </div>
                    <p class="d-flex align-items-center gap-2 mb-0 text-dark fw-medium">Pasta Primavera <span>x1</span></p>
                </div>

                <div class="status-item d-flex align-items-center justify-content-start gap-2">
                    <div class="avatar bg-warning rounded-circle flex-shrink-0">
                        <i class="icon-cooking-pot"></i>
                    </div>
                    <p class="d-flex align-items-center gap-2 mb-0 text-dark fw-medium">Chocolate Lava Cake <span>x2</span></p>
                </div>
            </div>

            <!-- Item 4 -->
            <div class="mb-4 pb-4 border-bottom">
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Sub Total<span class="fw-medium text-dark">$267</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Tax (10%)<span class="fw-medium text-dark"> $268</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Service Charge <span class="fw-medium text-dark"> $15</span> </h6>
            </div>
            <h5 class="mb-0 d-flex align-items-center justify-content-between">Total <span>$274</span></h5>

        </div>
    </div>
    <!-- End Wrapper -->

    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-between gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('orders')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Calculator -->
    <div class="modal fade pos-modal calci" id="calculator" tabindex="-1"  aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                    <div class="modal-header border-0">
                    <h4 class="modal-title">Add Discount</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body py-0">
                    <div class="calculator-wrap">
                        <div>
                            <div>
                                <label class="form-label">Amount<span class="text-danger"> *</span></label>
                                <input class="input form-control" type="text" placeholder="Amount or %" readonly>
                            </div>
                        </div>
                        <div class="calculator-body d-flex justify-content-between">
                            <div class="text-center">
                                <button class="btn btn-clear" onclick="clr()">C</button>
                                <button class="btn btn-number" onclick="dis('7')">7</button>
                                <button class="btn btn-number" onclick="dis('4')">4</button>
                                <button class="btn btn-number" onclick="dis('1')">1</button>
                                <button class="btn btn-number" onclick="dis(',')">,</button>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-expression" onclick="dis('/')">÷</button>
                                <button class="btn btn-number" onclick="dis('8')">8</button>
                                <button class="btn btn-number" onclick="dis('5')">5</button>
                                <button class="btn btn-number" onclick="dis('2')">2</button>
                                <button class="btn btn-number" onclick="dis('00')">00</button>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-expression" onclick="dis('%')">%</button>
                                <button class="btn btn-number" onclick="dis('9')">9</button>
                                <button class="btn btn-number" onclick="dis('6')">6</button>
                                <button class="btn btn-number" onclick="dis('3')">3</button>
                                <button class="btn btn-number" onclick="dis('.')">.</button>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-clear" onclick="back()"><i class="icon-arrow-left"></i></button>
                                <button class="btn btn-expression" onclick="dis('*')">x</button>
                                <button class="btn btn-expression" onclick="dis('-')">-</button>
                                <button class="btn btn-expression" onclick="dis('+')">+</button>
                                <button class="btn btn-clear" onclick="solve()">=</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                    <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary w-100 m-0">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Calculator -->
@endif

@if (Route::is(['kitchen']))
    <!-- Print details modal -->
    <div class="modal fade" id="print_order">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Print Reciept</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body">
                    <!-- Item 1 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h5 class="mb-3 fs-16">Order Info</h5>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Date & Time <span class="fw-medium text-dark">25/11/2026 - 08:45 PM</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Order No <span class="fw-medium text-dark"> #54654</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Token No <span class="fw-medium text-dark"> 20</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> No of Items <span class="fw-medium text-dark"> 4</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Order Type<span class="fw-medium text-dark"> Dine In (TabIe 4) </span> </h6>
                    </div>

                    <!-- Item 2 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h5 class="mb-3 fs-16">Ordered Menus</h5>
                        <h6 class="fs-14 fw-normal d-flex align-items-start justify-content-between mb-3 flex-column gap-2">Grilled Chicken ×1
                            <div class="bg-light rounded py-1 px-2">
                                <p class="mb-0 fw-medium d-flex align-items-center text-dark"> <i class="icon-badge-info me-1"></i> Notes : Extra Spicy,  With extra Pepperoni</p>
                            </div>
                        </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-start justify-content-between mb-3 flex-column gap-2"> Chicken Taco ×2
                        </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-start justify-content-between mb-3 flex-column gap-2"> Lobster Thermidor ×1
                            <div class="bg-light rounded py-1 px-2">
                                <p class="mb-0 fw-medium d-flex align-items-center text-dark"> <i class="icon-badge-info me-1"></i> Notes : Extra Spicy, Remove Scalp</p>
                            </div> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Grilled Chicken ×1 </h6>
                    </div>

                    <!-- Item 3 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Sub Total<span class="fw-medium text-dark">$267</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Tax (10%)<span class="fw-medium text-dark"> $268</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Service Charge <span class="fw-medium text-dark"> $15</span> </h6>
                    </div>
                    <h5 class="mb-0 d-flex align-items-center justify-content-between">Total <span>$274</span></h5>
                </div>

                <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                    <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary w-100 m-0">Print</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Modal  -->
    <div class="modal fade" id="cooking_started_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-orange-subtle">
                            <img src="{{URL::asset('build/img/icons/start-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Cooking Started</h4>
                    <p class="mb-4">Vegetarian Lasagna Cooking has
                        begun in the kitchen.</p>
                    <div class="d-flex justify-content-between gap-2">
                        <a href="#" class="btn btn-primary w-100" data-bs-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Start Modal  -->
    <div class="modal fade" id="cooking_done_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-success-subtle">
                            <img src="{{URL::asset('build/img/icons/checked-img.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Cooking Completed</h4>
                    <p class="mb-4">Order <span class="text-dark fw-semibold">#14751</span> has been completed & ready in kitchen to serve</p>
                    <div class="d-flex justify-content-between gap-2">
                        <a href="#" class="btn btn-primary w-100" data-bs-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Start Modal  -->
    <div class="modal fade" id="order_pause">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-info-subtle">
                            <img src="{{URL::asset('build/img/icons/pause-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Order Paused</h4>
                    <p class="mb-4">Order <span class="text-dark fw-semibold">#14751</span> has been paused in the kitchen</p>
                    <div class="d-flex justify-content-between gap-2">
                        <a href="#" class="btn btn-primary w-100" data-bs-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->
@endif

@if (Route::is(['orders']))
    <!-- Print details modal -->
    <div class="modal fade" id="print_reciept">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Print Reciept</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body">
                    <!-- Item 1 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h5 class="mb-3 fs-16">Order Info</h5>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Date & Time <span class="fw-medium text-dark">25/11/2026 - 08:45 PM</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Order No <span class="fw-medium text-dark"> #54654</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Token No <span class="fw-medium text-dark"> 20</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> No of Items <span class="fw-medium text-dark"> 4</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Order Type<span class="fw-medium text-dark"> Dine In (TabIe 4) </span> </h6>
                    </div>

                    <!-- Item 2 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h5 class="mb-3 fs-16">Ordered Menus</h5>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Grilled Chicken ×1 <span class="fw-medium text-dark">$49</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Chicken Taco ×2 <span class="fw-medium text-dark"> $66</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Lobster Thermidor ×1 <span class="fw-medium text-dark"> $76</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Grilled Chicken ×1 <span class="fw-medium text-dark"> $62</span> </h6>
                    </div>

                    <!-- Item 3 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Sub Total<span class="fw-medium text-dark">$267</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Tax (10%)<span class="fw-medium text-dark"> $268</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Service Charge <span class="fw-medium text-dark"> $15</span> </h6>
                    </div>
                    <h5 class="mb-0 d-flex align-items-center justify-content-between">Total <span>$274</span></h5>
                </div>

                <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                    <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary w-100 m-0">Print</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pay & Complete Order modal -->
    <div class="modal fade" id="pay_complete_order">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Pay & Complete Order</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 border rounded mb-4">
                        <h3 class="text-center mb-0">Final Total : $274</h3>
                    </div>
                    <!-- start row -->
                    <div class="row g-4">
                        <div class="col-lg-6 border-end">
                            <!-- Item 1 -->
                            <div class="mb-3 pb-3 border-bottom">
                                <h5 class="mb-3 fs-16">Order Info</h5>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Order No <span class="fw-medium text-dark"> #54654</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> No of Items <span class="fw-medium text-dark"> 4</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Order Type<span class="fw-medium text-dark"> Dine In (TabIe 4) </span> </h6>
                            </div>

                            <!-- Item 2 -->
                            <div class="mb-3 pb-3 border-bottom orders-list">
                                <h5 class="mb-3 fs-16">Ordered Menus</h5>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 orders-two">Grilled Chicken ×1 <span class="line"></span><span class="fw-medium text-dark">$49</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 orders-two"> Chicken Taco ×2 <span class="line"></span><span class="fw-medium text-dark"> $66</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 orders-two"> Lobster Thermidor ×1 <span class="line"></span><span class="fw-medium text-dark"> $76</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0 orders-two"> Grilled Chicken ×1 <span class="line"></span><span class="fw-medium text-dark"> $62</span> </h6>
                            </div>

                            <!-- Item 3 -->
                            <div>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Sub Total<span class="fw-medium text-dark">$267</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Tax (10%)<span class="fw-medium text-dark"> $268</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Discount (15%)<span class="fw-medium text-dark"> $26.7</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Service Charge <span class="fw-medium text-dark"> $15</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Coupon (FIRSTORDER) <span class="fw-medium text-danger"> -$45</span> </h6>
                                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Tip <span class="fw-medium text-dark"> $20</span> </h6>
                            </div>

                        </div>
                        <!-- end cold -->
                        <div class="col-lg-6">
                            <div>
                                <h6 class="mb-3">Payment Type</h6>
                                <ul class="nav nav-tabs nav-tabs-solid border-0 flex-nowrap mb-4" role="tablist">
                                    <li class="nav-item w-100">
                                        <a href="#order-tab6" class="nav-link active d-flex align-items-center justify-content-center w-100" data-bs-toggle="tab"><i class="icon-dollar-sign me-1"></i>Cash</a>
                                    </li>
                                    <li class="nav-item w-100">
                                        <a href="#order-tab7" class="nav-link d-flex align-items-center justify-content-center w-100" data-bs-toggle="tab"><i class="icon-credit-card me-1"></i>Card</a>
                                    </li>
                                    <li class="nav-item w-100">
                                        <a href="#order-tab8" class="nav-link d-flex align-items-center justify-content-center w-100" data-bs-toggle="tab"><i class="icon-scan-text me-1"></i>Scan</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Item 1 -->
                                    <div class="tab-pane show active" id="order-tab6">
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Discount <span class="line"></span> <a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#calculator"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Tips <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_tips_calci"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Coupon <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Given Amount<span class="text-danger"> *</span></label>
                                                    <input type="text" class="form-control" value="300">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Balance<span class="text-danger"> *</span></label>
                                                    <input type="text" class="form-control" value="26">
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold">Note</label>
                                            <textarea rows="4" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <!-- Item 2 -->
                                    <div class="tab-pane" id="order-tab7">
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Discount <span class="line"></span> <a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#calculator"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Tips <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_tips_calci"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Coupon <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="mb-4 text-center p-4 border rounded d-flex align-items-center justify-content-center flex-column gap-2">
                                            <img src="{{URL::asset('build/img/icons/mobile-phone.svg')}}" alt="Mobile" class="img-fluid d-block custom-line-img-two">
                                            Tap or Swipe your card to pay
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold">Note</label>
                                            <textarea rows="4" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <!-- Item 3 -->
                                    <div class="tab-pane" id="order-tab8">
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Discount <span class="line"></span> <a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#calculator"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Tips <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_tips_calci"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4 fw-medium text-dark">Coupon <span class="line"></span><a href="#" class="btn btn-sm btn-outline-light d-flex align-items-center"><i class="icon-plus me-1"></i>Add</a> </div>
                                        <div class="mb-4 text-center p-4 border rounded d-flex align-items-center justify-content-center flex-column gap-2">
                                            <img src="{{URL::asset('build/img/icons/qr-img.svg')}}" alt="Mobile" class="img-fluid d-block custom-line-img-two">
                                            Scan with your UPI app to pay
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold">Note</label>
                                            <textarea rows="4" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>

                <div class="modal-footer d-flex align-items-center justify-content-end flex-nowrap gap-2">
                    <button type="button" class="btn btn-light m-0" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary m-0">Pay & Complete Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- start offcanvas -->
    <div class="offcanvas offcanvas-offset offcanvas-end" tabindex="-1"  id="view_details">
        <div class="offcanvas-header d-block border-bottom">
            <div class="d-flex align-items-center justify-content-between">
            <h4 class="title mb-0">Order : #22154</h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button></div>
        </div>
        <div class="offcanvas-body">

            <!-- Item 1 -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                        <h6 class="mb-0">Order Status</h6>
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-1"><i class="icon-clock fs-14"></i> 06:24 PM</h6>
                    </div>
                    <div class="orders-list-two">
                        <!-- start row -->
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="status-item text-center">
                                    <div class="avatar bg-primary rounded-circle mb-2">
                                        <i class="icon-check"></i>
                                    </div>
                                    <p class="mb-0">Accepted</p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="status-item text-center">
                                    <div class="avatar bg-warning rounded-circle mb-2">
                                        <i class="icon-cooking-pot"></i>
                                    </div>
                                    <p class="mb-0">In Kitchen</p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="status-item text-center">
                                    <div class="avatar bg-light rounded-circle mb-2 text-dark">
                                        <i class="icon-flag"></i>
                                    </div>
                                    <p class="mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="mb-4 pb-4 border-bottom">
                <h5 class="mb-3 fs-16">Order Info</h5>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 text-body">Date & Time <span class="fw-medium text-dark">25/11/2026 - 08:45 PM</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 text-body"> Order No <span class="fw-medium text-dark"> #54654</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 text-body"> Token No <span class="fw-medium text-dark"> 20</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3 text-body"> No of Items <span class="fw-medium text-dark"> 4</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0 text-body"> Order Type<span class="fw-medium text-dark"> Dine In (TabIe 4) </span> </h6>
            </div>

            <!-- Item 3 -->
            <div class="mb-4 pb-4 border-bottom orders-list-three">
                <h5 class="mb-3 fs-16">Items</h5>
                <div class="status-item mb-4 d-flex align-items-center justify-content-start gap-2">
                    <div class="avatar bg-success rounded-circle flex-shrink-0">
                        <i class="icon-flag"></i>
                    </div>
                    <div>
                        <p class="d-flex align-items-center gap-2 mb-2 text-dark fw-medium">Margherita Pizza <span>x1</span></p>
                        <div class="bg-light rounded py-1 px-2">
                            <p class="mb-0 fw-medium d-flex align-items-center text-dark"> <i class="icon-badge-info me-1"></i> Notes : Extra Spicy,  With extra Pepperoni</p>
                        </div>
                    </div>
                </div>

                <div class="status-item mb-4 d-flex align-items-center justify-content-start gap-2">
                    <div class="avatar bg-warning rounded-circle flex-shrink-0">
                        <i class="icon-cooking-pot"></i>
                    </div>
                    <p class="d-flex align-items-center gap-2 mb-0 text-dark fw-medium">Pasta Primavera <span>x1</span></p>
                </div>

                <div class="status-item d-flex align-items-center justify-content-start gap-2">
                    <div class="avatar bg-warning rounded-circle flex-shrink-0">
                        <i class="icon-cooking-pot"></i>
                    </div>
                    <p class="d-flex align-items-center gap-2 mb-0 text-dark fw-medium">Chocolate Lava Cake <span>x2</span></p>
                </div>
            </div>

            <!-- Item 4 -->
            <div class="mb-4 pb-4 border-bottom">
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Sub Total<span class="fw-medium text-dark">$267</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Tax (10%)<span class="fw-medium text-dark"> $268</span> </h6>
                <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Service Charge <span class="fw-medium text-dark"> $15</span> </h6>
            </div>
            <h5 class="mb-0 d-flex align-items-center justify-content-between">Total <span>$274</span></h5>

        </div>
    </div>
    <!-- End Wrapper -->

    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-between gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('orders')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Calculator -->
    <div class="modal fade pos-modal calci" id="calculator" tabindex="-1"  aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                    <div class="modal-header border-0">
                    <h4 class="modal-title">Add Discount</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body py-0">
                    <div class="calculator-wrap">
                        <div>
                            <div>
                                <label class="form-label">Amount<span class="text-danger"> *</span></label>
                                <input class="input form-control" type="text" placeholder="Amount or %" readonly>
                            </div>
                        </div>
                        <div class="calculator-body d-flex justify-content-between">
                            <div class="text-center">
                                <button class="btn btn-clear" onclick="clr()">C</button>
                                <button class="btn btn-number" onclick="dis('7')">7</button>
                                <button class="btn btn-number" onclick="dis('4')">4</button>
                                <button class="btn btn-number" onclick="dis('1')">1</button>
                                <button class="btn btn-number" onclick="dis(',')">,</button>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-expression" onclick="dis('/')">÷</button>
                                <button class="btn btn-number" onclick="dis('8')">8</button>
                                <button class="btn btn-number" onclick="dis('5')">5</button>
                                <button class="btn btn-number" onclick="dis('2')">2</button>
                                <button class="btn btn-number" onclick="dis('00')">00</button>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-expression" onclick="dis('%')">%</button>
                                <button class="btn btn-number" onclick="dis('9')">9</button>
                                <button class="btn btn-number" onclick="dis('6')">6</button>
                                <button class="btn btn-number" onclick="dis('3')">3</button>
                                <button class="btn btn-number" onclick="dis('.')">.</button>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-clear" onclick="back()"><i class="icon-arrow-left"></i></button>
                                <button class="btn btn-expression" onclick="dis('*')">x</button>
                                <button class="btn btn-expression" onclick="dis('-')">-</button>
                                <button class="btn btn-expression" onclick="dis('+')">+</button>
                                <button class="btn btn-clear" onclick="solve()">=</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                    <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary w-100 m-0">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Calculator -->
@endif

@if (Route::is(['payments']))
    <!-- Start Filter -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filter-offcanvas">
        <div class="offcanvas-header pb-0">
            <div class="border-bottom d-flex align-items-center justify-content-between w-100 pb-3">
                <h4 class="offcanvas-title mb-0">Filter</h4>
                <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button>
            </div>
        </div>
        <div class="offcanvas-body d-flex flex-column pt-3">
            <div>
                <div class="mb-3">
                    <label class="form-label">Customer<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Customer</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Adrian James
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Sue Allen
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Frank Barrett
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Kelley Davis
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Jim Vickers
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Type</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Dine In
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Take Away
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Delivery
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="d-flex align-items-center gap-2 mt-auto border-0">
                    <a href="#" class="btn btn-light w-100">Reset</a>
                    <a href="#" class="btn btn-primary w-100">Apply</a>
                </div>

        </div>
    </div>
    <!-- End Filter -->
@endif

@if (Route::is(['pos']))
    <!-- Order Modal  -->
    <div class="modal fade" id="order_modal">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-success-subtle shadow-lg">
                            <img src="{{URL::asset('build/img/icons/checked-img.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Order Sent to Kitchen</h4>
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                        <p class="mb-0"> Token No : <span class="text-dark">7</span> </p>
                        <span class="even-line"></span>
                        <p class="mb-0"> Order ID : <span class="text-dark">#26598</span> </p>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <a href="{{url('pos')}}" class="btn btn-light w-100">Close</a>
                        <a href="{{url('pos')}}" class="btn btn-primary w-100">Print Token</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Add Note modal -->
    <div class="modal fade" id="add_notes">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Add Note</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('pos')}}">
                    <div class="modal-body">
                        <label class="form-label">Notes<span class="text-danger"> *</span></label>
                        <textarea rows="4" class="form-control"></textarea>
                        <p class="fw-medium mb-0 mt-1">Add Minimum 200 Characters</p>
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                        <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary w-100 m-0">Add Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Details modal -->
    <div class="modal fade" id="items_details">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-body p-4">

                    <!-- start row -->
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="items-img p-3 border rounded bg-light">
                                <img src="{{URL::asset('build/img/food/food-img-1.png')}}" alt="food" class="img-fluid img-1">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="items-content">
                                <h4 class="mb-2">Chicken Taco</h4>
                                <p class="mb-3">Tender grilled chicken wrapped in soft tortillas, topped with fresh lettuce, salsa, and creamy sauce.</p>
                                <div class="items-info mb-4 pb-4 border-bottom">
                                    <h6 class="fw-semibold mb-3">Sizes</h6>
                                    <div class="d-flex align-items-center flex-wrap gap-2 size-group">
                                        <div class="size-tab">
                                            <button class="tag d-flex align-items-center justify-content-between gap-3">Small <span>$28</span> </button>
                                        </div>
                                        <div class="size-tab">
                                            <button class="tag d-flex align-items-center justify-content-between gap-3">Medium <span>$28</span></button>
                                        </div>
                                        <div class="size-tab">
                                            <button class="tag d-flex align-items-center justify-content-between gap-3">Regular <span>$28</span></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4 pb-4 border-bottom">
                                    <h6 class="fw-semibold mb-3">Add-ons & Upgrades</h6>
                                    <div class="upgrade-slider">
                                        <div class="slider-item">
                                            <div class="d-flex align-items-center gap-2 border p-2 rounded addon-item">
                                                <div class="avatar rounded-circle border">
                                                    <img src="{{URL::asset('build/img/food/food-1.png')}}" alt="food" class="img-fluid img-1">
                                                </div>
                                                <div>
                                                    <p class="fw-medium mb-1 text-dark">Extra Chicken</p>
                                                    <p class="mb-0 fw-medium">$2</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="slider-item">
                                            <div class="d-flex align-items-center gap-2 border p-2 rounded addon-item">
                                                <div class="avatar rounded-circle border">
                                                    <img src="{{URL::asset('build/img/food/food-2.png')}}" alt="food" class="img-fluid img-1">
                                                </div>
                                                <div>
                                                    <p class="fw-medium mb-1 text-dark">Grilled Chicken</p>
                                                    <p class="mb-0 fw-medium">$8</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="slider-item">
                                            <div class="d-flex align-items-center gap-2 border p-2 rounded addon-item">
                                                <div class="avatar rounded-circle border">
                                                    <img src="{{URL::asset('build/img/food/food-3.png')}}" alt="food" class="img-fluid img-1">
                                                </div>
                                                <div>
                                                    <p class="fw-medium mb-1 text-dark">Chicken Soup</p>
                                                    <p class="mb-0 fw-medium">$2</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-4 d-flex align-items-center justify-content-between">Total <span>$274</span></h5>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="quantity-control">
                                            <button type="button" class="minus-btn"><i class="icon-minus"></i></button>
                                            <input type="text" class="quantity-input" value="1" aria-label="Quantity">
                                            <button type="button" class="add-btn"><i class="icon-plus"></i></button>
                                        </div>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#item_view" class="btn btn-primary w-100 d-flex align-items-center gap-2"> <i class="icon-shopping-bag"></i> Add to Cart</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Details modal -->
    <div class="modal fade" id="transactions_details">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Transactions</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body p-4">

                    <ul class="nav nav-tabs nav-tabs-solid border-0 mb-3 pb-3 border-bottom align-items-center flex-wrap gap-3" role="tablist">
                        <li class="nav-item">
                            <a href="#order-tab15" class="nav-link active shadow-sm fw-medium d-flex  gap-2 align-items-center" data-bs-toggle="tab"><i class="icon-circle-dollar-sign"></i>Sale</a>
                        </li>
                        <li class="nav-item">
                            <a href="#order-tab16" class="nav-link shadow-sm fw-medium d-flex align-items-center gap-2" data-bs-toggle="tab"><i class="icon-file-scan"></i>Draft</a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                        <div class="input-group input-group-flat w-auto">
                            <input type="text" class="form-control" placeholder="Search">
                            <span class="input-group-text">
                                <i class="icon-search text-dark"></i>
                            </span>
                        </div>
                        <!-- sort by -->
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                Sort by : Newest
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Newest</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Oldest</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Ascending</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Descending</a>
                                    </li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane show active" id="order-tab15">
                            <!-- table start -->
                            <div class="table-responsive table-nowrap">
                                <table class="table mb-0 border">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Ref</th>
                                            <th>Customer</th>
                                            <th>Grand Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23588</td>
                                            <td>Walk-in Customer</td>
                                            <td>
                                                $34.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-printer"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23587</td>
                                            <td>Sue Allen</td>
                                            <td>$78.20</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-printer"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23586</td>
                                            <td>Frank Barrett</td>
                                            <td>$45.10</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-printer"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23585</td>
                                            <td>Kelley Davis</td>
                                            <td>$92.80</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-printer"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23584</td>
                                            <td>Jim Vickers</td>
                                            <td>$61.40</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-printer"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23583</td>
                                            <td>Nancy Chapman</td>
                                            <td>$57.20</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-printer"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23582</td>
                                            <td>Ron Jude</td>
                                            <td>$45.30</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-printer"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                31 Oct 2026
                                            </td>
                                            <td>#23581</td>
                                            <td>Andrea Aponte</td>
                                            <td>$72.60</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-printer"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                31 Oct 2026
                                            </td>
                                            <td>#23580</td>
                                            <td>David Belcher</td>
                                            <td>$32.10</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-printer"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                31 Oct 2026
                                            </td>
                                            <td>#23579</td>
                                            <td>Julie Kangas</td>
                                            <td>$40.30</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-printer"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <!-- table end -->
                        </div>

                        <div class="tab-pane show" id="order-tab16">
                            <!-- table start -->
                            <div class="table-responsive table-nowrap">
                                <table class="table mb-0 border">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Ref</th>
                                            <th>Customer</th>
                                            <th>Grand Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                01 Dec 2026
                                            </td>
                                            <td>#23588</td>
                                            <td>Walk-in Customer</td>
                                            <td>
                                                $34.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23588</td>
                                            <td>Walk-in Customer</td>
                                            <td>
                                                $34.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23587</td>
                                            <td>Sue Allen</td>
                                            <td>
                                                $54.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23586</td>
                                            <td>Frank Barrett</td>
                                            <td>
                                                $94.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23584</td>
                                            <td>Kelley Davis</td>
                                            <td>
                                                $14.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23588</td>
                                            <td>Walk-in Customer</td>
                                            <td>
                                                $34.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23588</td>
                                            <td>Jim Vickers</td>
                                            <td>
                                                $19.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23582</td>
                                            <td>Nancy Chapman</td>
                                            <td>
                                                $34.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                31 Dec 2026
                                            </td>
                                            <td>#23579</td>
                                            <td>Ron Jude</td>
                                            <td>
                                                $34.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                01 Nov 2026
                                            </td>
                                            <td>#23588</td>
                                            <td>Walk-in Customer</td>
                                            <td>
                                                $34.50
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <!-- table end -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Details modal -->
    <div class="modal fade" id="draft_details">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Draft</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body p-4">

                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                        <div class="input-group input-group-flat w-auto">
                            <input type="text" class="form-control" placeholder="Search">
                            <span class="input-group-text">
                                <i class="icon-search text-dark"></i>
                            </span>
                        </div>

                        <!-- sort by -->
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                Sort by : Newest
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Newest</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Oldest</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Ascending</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Descending</a>
                                    </li>
                            </ul>
                        </div>
                    </div>

                    <!-- table start -->
                    <div class="table-responsive table-nowrap">
                        <table class="table mb-0 border">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Ref</th>
                                    <th>Customer</th>
                                    <th>Grand Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        01 Dec 2026
                                    </td>
                                    <td>#23588</td>
                                    <td>Walk-in Customer</td>
                                    <td>
                                        $34.50
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        01 Nov 2026
                                    </td>
                                    <td>#23588</td>
                                    <td>Walk-in Customer</td>
                                    <td>
                                        $34.50
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        01 Nov 2026
                                    </td>
                                    <td>#23587</td>
                                    <td>Sue Allen</td>
                                    <td>
                                        $54.50
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        01 Nov 2026
                                    </td>
                                    <td>#23586</td>
                                    <td>Frank Barrett</td>
                                    <td>
                                        $94.50
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        01 Nov 2026
                                    </td>
                                    <td>#23584</td>
                                    <td>Kelley Davis</td>
                                    <td>
                                        $14.50
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        01 Nov 2026
                                    </td>
                                    <td>#23588</td>
                                    <td>Walk-in Customer</td>
                                    <td>
                                        $34.50
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        01 Nov 2026
                                    </td>
                                    <td>#23588</td>
                                    <td>Jim Vickers</td>
                                    <td>
                                        $19.50
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        01 Nov 2026
                                    </td>
                                    <td>#23582</td>
                                    <td>Nancy Chapman</td>
                                    <td>
                                        $34.50
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        31 Dec 2026
                                    </td>
                                    <td>#23579</td>
                                    <td>Ron Jude</td>
                                    <td>
                                        $34.50
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        01 Nov 2026
                                    </td>
                                    <td>#23588</td>
                                    <td>Walk-in Customer</td>
                                    <td>
                                        $34.50
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-pencil-line"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- table end -->

                </div>
            </div>
        </div>
    </div>

    <!-- Print details modal -->
    <div class="modal fade" id="print_reciept">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Print Reciept</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body">
                    <!-- Item 1 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h5 class="mb-3 fs-16">Order Info</h5>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Date & Time <span class="fw-medium text-dark">25/11/2026 - 08:45 PM</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Order No <span class="fw-medium text-dark"> #54654</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Token No <span class="fw-medium text-dark"> 20</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> No of Items <span class="fw-medium text-dark"> 4</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Order Type<span class="fw-medium text-dark"> Dine In (TabIe 4) </span> </h6>
                    </div>

                    <!-- Item 2 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h5 class="mb-3 fs-16">Ordered Menus</h5>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Grilled Chicken ×1 <span class="fw-medium text-dark">$49</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Chicken Taco ×2 <span class="fw-medium text-dark"> $66</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Lobster Thermidor ×1 <span class="fw-medium text-dark"> $76</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Grilled Chicken ×1 <span class="fw-medium text-dark"> $62</span> </h6>
                    </div>

                    <!-- Item 3 -->
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3">Sub Total<span class="fw-medium text-dark">$267</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-3"> Tax (10%)<span class="fw-medium text-dark"> $268</span> </h6>
                        <h6 class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"> Service Charge <span class="fw-medium text-dark"> $15</span> </h6>
                    </div>
                    <h5 class="mb-0 d-flex align-items-center justify-content-between">Total <span>$274</span></h5>
                </div>

                <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                    <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary w-100 m-0">Print</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Customer modal -->
    <div class="modal fade" id="add_customer">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Add  Customer</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('pos')}}">
                    <div class="modal-body pt-0">

                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div>
                                    <label class="form-label">Customer Name <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div>
                                    <label class="form-label">Phone <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div>
                                    <label class="form-label">Email <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Date of Birth<span class="text-danger ms-1">*</span></label>
                                <div class="input-group w-auto input-group-flat">
                                    <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="dd/mm/yyyy">
                                    <span class="input-group-text">
                                        <i class="icon-calendar-fold"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Gender<span class="text-danger"> *</span></label>
                                <select class="select">
                                    <option>Select</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Otherwise</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                        <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary w-100 m-0">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Customer modal (dynamic: action and fields set from .edit-customer-btn data-* when opened from customer page) -->
    <div class="modal fade" id="edit_customer">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Edit Customer</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form id="edit_customer_form" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body pt-0">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_customer_name" class="form-control" required>
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" id="edit_customer_phone" class="form-control">
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="edit_customer_email" class="form-control">
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="text" name="date_of_birth" class="form-control" data-provider="flatpickr" data-date-format="Y-m-d" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                        <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary w-100 m-0">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    (function() {
        var el = document.getElementById('edit_customer');
        if (!el) return;
        el.addEventListener('show.bs.modal', function(e) {
            var trigger = e.relatedTarget;
            var form = document.getElementById('edit_customer_form');
            if (!form) return;
            if (trigger && trigger.classList.contains('edit-customer-btn') && trigger.dataset.id) {
                form.action = '{{ url("customer") }}/' + trigger.dataset.id;
                var nameInp = document.getElementById('edit_customer_name');
                var phoneInp = document.getElementById('edit_customer_phone');
                var emailInp = document.getElementById('edit_customer_email');
                if (nameInp) nameInp.value = trigger.dataset.name || '';
                if (phoneInp) phoneInp.value = trigger.dataset.phone || '';
                if (emailInp) emailInp.value = trigger.dataset.email || '';
            }
        });
    })();
    </script>

    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-between gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('pos')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Start Modal  -->
    <div class="modal fade" id="item_view">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-success-subtle">
                            <img src="{{URL::asset('build/img/icons/checked-img.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Item Added</h4>
                    <p class="mb-0">Lobster Thermidor Added to the cart</p>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- View Invoices -->
    <div class="modal fade" id="view_invoices">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h5 class="modal-title">Invoice</h5>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                    <div class="modal-body p-4 pt-1">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="row g-4 justify-content-between align-items-center border-bottom pb-4">
                                        <div class="col-md-6">
                                            <h6 class="mb-2">#INV5465</h6>
                                            <h6>DreamsPOS</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2 invoice-logo d-flex align-items-center justify-content-md-end justify-content-start">
                                                <span class="app-brand-logo app-brand-logo--invoice"><img src="{{URL::asset('build/img/logo.svg')}}" width="130" class="img-fluid logo" alt="logo"></span>
                                                <span class="app-brand-logo app-brand-logo--invoice"><img src="{{URL::asset('build/img/logo-white.svg')}}" width="130" class="img-fluid logo-white d-none" alt="logo"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="row g-4 justify-content-between align-items-center border-bottom pb-4">
                                        <div class="col-md-4">
                                            <h6 class="mb-2">Invoice From</h6>
                                            <p class="text-dark fw-semibold mb-2">DreamsPOS</p>
                                            <p class="mb-2">15 Hodges Mews, High Wycombe HP12 3JL,United Kingdom</p>
                                            <p class="mb-0">Phone : +1 45659 96566</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-2">Bill To </h6>
                                            <p class="text-dark fw-semibold mb-2">Andrew Fletcher</p>
                                            <p class="mb-2">1147 Rohan Drive Suite,Burlington, VT / 8202115 United Kingdom</p>
                                            <p class="mb-0">Phone : +1 45659 96566</p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center justify-content-md-center">
                                                <img src="{{URL::asset('build/img/invoices/paid-invoices.svg')}}" alt="paid-invoices-img">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <h6 class="mb-3">Items Details</h6>
                                    <div class="table-responsive table-nowrap">
                                        <table class="table mb-0 border ">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item Details</th>
                                                    <th>Quantity</th>
                                                    <th>Rate</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Grilled Salmon Steak</td>
                                                    <td>2</td>
                                                    <td>$200.00</td>
                                                    <td>$396.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Crispy Bacon Bits</td>
                                                    <td>1</td>
                                                    <td>$350.00</td>
                                                    <td>$365.75</td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>Side Fries</td>
                                                    <td>1</td>
                                                    <td>$399.00</td>
                                                    <td>$398.90</td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>Lemon Wedge</td>
                                                    <td>4</td>
                                                    <td>$100.00</td>
                                                    <td>$396.00</td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <div class="row g-4 justify-content-between align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-2">Terms and Conditions</h6>
                                            <div class="mb-4">
                                                <p class="mb-0">1. Goods once sold cannot be taken back or exchanged.</p>
                                                <p class="mb-0">2. We are not the manufacturers the company provides warranty</p>
                                            </div>
                                            <div class="px-3 py-2 bg-light">
                                                <p class="mb-0">Note : Please ensure payment is made within 7 days of invoice date.</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-6">
                                            <div class="mb-3">
                                                <div class="row align-items-center pb-3 border-bottom">
                                                    <div class="col-6">
                                                        <p class="text-dark fw-semibold mb-3">Amount</p>
                                                        <p class="text-dark fw-semibold mb-3">CGST (9%)</p>
                                                        <p class="text-dark fw-semibold mb-3">SGST (9%)</p>
                                                        <p class="text-dark fw-semibold mb-0">Discount (25%)</p>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <p class="text-dark fw-semibold mb-3">$1,793.12</p>
                                                        <p class="text-dark fw-semibold mb-3">$18</p>
                                                        <p class="text-dark fw-semibold mb-3">$18</p>
                                                        <p class="text-danger fw-semibold mb-0">- $18</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between algin-item-center">
                                                <h6>Total ($)</h6>
                                                <h6>$1,972.43</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center algin-item-center">
                            <div class="d-flex justify-content-center algin-item-center flex-wrap gap-3">
                                <button type="button" class="btn btn-white d-flex align-items-center"><i class="icon-download me-1"></i>Download PDF</button>
                                <button type="button" class="btn btn-white d-flex align-items-center"><i class="icon-printer me-1"></i>Print Invoice</button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <!-- View Invoices End -->
@endif

@if (Route::is(['reservations']))
    <!-- Add reservation modal -->
    <div class="modal fade" id="add_reservation">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Add  Reservation</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('reservations.store') }}" id="form-add-reservation">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <label class="form-label">Customer name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="customer_name" value="{{ old('customer_name') }}" placeholder="Customer name" required>
                                @error('customer_name')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Customer phone</label>
                                <input type="text" class="form-control" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="Phone">
                                @error('customer_phone')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="reservation_date" value="{{ old('reservation_date') }}" required>
                                @error('reservation_date')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Time</label>
                                <input type="time" class="form-control" name="reservation_time" value="{{ old('reservation_time') }}">
                                @error('reservation_time')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Table</label>
                                <select class="form-select" name="restaurant_table_id">
                                    <option value="">Select</option>
                                    @foreach(($tables ?? collect()) as $t)
                                        <option value="{{ $t->id }}" {{ old('restaurant_table_id') == $t->id ? 'selected' : '' }}>{{ $t->name ?? 'Table ' . $t->id }}</option>
                                    @endforeach
                                </select>
                                @error('restaurant_table_id')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">No of Guests <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="guests" value="{{ old('guests', 1) }}" min="1" max="99" required>
                                @error('guests')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="">Select</option>
                                    <option value="booked" {{ old('status', 'booked') === 'booked' ? 'selected' : '' }}>Booked</option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="no-show" {{ old('status') === 'no-show' ? 'selected' : '' }}>No-show</option>
                                </select>
                                @error('status')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Notes</label>
                                <textarea rows="3" class="form-control" name="notes" placeholder="Notes">{{ old('notes') }}</textarea>
                                @error('notes')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                    <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="form-add-reservation" class="btn btn-primary w-100 m-0">Save Reservation</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit reservation modal -->
    <div class="modal fade" id="edit_reservation">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Edit  Reservation</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <div class="modal-body">
                    <form action="#">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div>
                                    <label class="form-label">Customer <span class="text-danger"> *</span></label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option selected>Adrian Hawk ( +1 66898 98985 )</option>
                                        <option selected>Adam Clark - +1 45474 21456</option>
                                        <option>Create “Ad”</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Date & Time <span class="text-danger ms-1">*</span></label>
                                <div class="input-group w-auto input-group-flat">
                                    <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="10/05/2026">
                                    <span class="input-group-text">
                                        <i class="icon-calendar-fold"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Table <span class="text-danger"> *</span></label>
                                <select class="select">
                                    <option>Select</option>
                                    <option selected>Ethan Sullivan</option>
                                    <option selected>Matthew Collins</option>
                                    <option>Olivia Reed</option>
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">No of Guests <span class="text-danger"> *</span></label>
                                <input type="number" class="form-control" value="10">
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Status <span class="text-danger"> *</span></label>
                                <select class="select">
                                    <option>Select</option>
                                    <option selected>Booked</option>
                                    <option>Cancelled</option>
                                    <option>Paid</option>
                                </select>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Notes<span class="text-danger"> *</span></label>
                                <textarea rows="4" class="form-control"></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                    <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary w-100 m-0">Save Reservation</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-between gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('reservations')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Add Customer modal -->
    <div class="modal fade" id="add_customer">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Add Customer</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('reservations')}}">
                    <div class="modal-body pt-0">

                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div>
                                    <label class="form-label">Customer Name <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div>
                                    <label class="form-label">Phone <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div>
                                    <label class="form-label">Email <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Date of Birth</label>
                                <div class="input-group w-auto input-group-flat">
                                    <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="dd/mm/yyyy">
                                    <span class="input-group-text">
                                        <i class="icon-calendar-fold"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Gender</label>
                                <select class="select">
                                    <option>Select</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Otherwise</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2">
                        <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary w-100 m-0">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@if (Route::is(['role-permission']))
    <!-- Add Role -->
    <div class="modal fade" id="add_role">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Add Role</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('role-permission')}}">
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label">Role Name<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Role End -->
@endif

@if (Route::is(['table']))
    <!-- Add Table -->
    <div class="modal fade" id="add_table">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Add Table</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{ route('table.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label">Table Number</label>
                            <input type="number" name="table_number" class="form-control" min="1" max="999" placeholder="e.g. 1, 2, 10">
                            <div class="form-text">Used for sorting. Optional.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Table Name<span class="text-danger"> *</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Floor</label>
                            <input type="text" name="floor" class="form-control" placeholder="e.g. 1st, 2nd">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="4" min="1" max="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="reserved">Reserved</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Table End -->

    <!-- Edit Table -->
    <div class="modal fade" id="edit_table">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Edit Table</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form id="edit_table_form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label">Table Number</label>
                            <input type="number" name="table_number" id="edit_table_number" class="form-control" min="1" max="999" placeholder="e.g. 1, 2, 10">
                            <div class="form-text">Used for sorting. Optional.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Table Name<span class="text-danger"> *</span></label>
                            <input type="text" name="name" id="edit_table_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Floor</label>
                            <input type="text" name="floor" id="edit_table_floor" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" id="edit_table_capacity" class="form-control" min="1" max="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_table_status" class="form-select">
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="reserved">Reserved</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.querySelectorAll('.edit-table-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var form = document.getElementById('edit_table_form');
            form.action = '{{ url("table") }}/' + id;
            document.getElementById('edit_table_number').value = this.dataset.tableNumber || '';
            document.getElementById('edit_table_name').value = this.dataset.name || '';
            document.getElementById('edit_table_floor').value = this.dataset.floor || '';
            document.getElementById('edit_table_capacity').value = this.dataset.capacity || '4';
            document.getElementById('edit_table_status').value = this.dataset.status || 'available';
        });
    });
    </script>
    <!-- Edit Table End -->

    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="#" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Start Filter -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filter-offcanvas">
        <div class="offcanvas-header pb-0">
            <div class="border-bottom d-flex align-items-center justify-content-between w-100 pb-3">
                <h4 class="offcanvas-title mb-0">Filter</h4>
                <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button>
            </div>
        </div>
        <div class="offcanvas-body d-flex flex-column pt-3">
            <div>
                <div class="mb-3">
                    <label class="form-label">Category<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Category</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Sea Food
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Pizza
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Salads
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Tacos
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Burgers
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status<span class="text-danger"> *</span></label>
                    <select class="select">
                        <option>Select</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 mt-auto">
                <a href="#" class="btn btn-light w-100">Reset</a>
                <a href="#" class="btn btn-primary w-100">Apply</a>
            </div>
        </div>
    </div>
    <!-- End Filter -->
@endif

@if (Route::is(['tax-settings']))
    <!-- Add Category -->
    <div class="modal fade" id="add_tax">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Add Tax</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('tax-settings')}}">
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label">Title<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tax Rate (%)<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tax Type<span class="text-danger"> *</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option>Inclusive / Exclusive</option>
                                <option>Exclusive</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Category End -->

    <!-- Edit Category -->
    <div class="modal fade" id="edit_tax">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Edit Tax</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('tax-settings')}}">
                    <div class="modal-body p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label">Title<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" value="Inclusive / Exclusive">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tax Rate (%)<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" value="9%">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tax Type<span class="text-danger"> *</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option selected>Inclusive / Exclusive</option>
                                <option>Exclusive</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Category End -->

    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('tax-settings')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->
@endif

@if (Route::is(['users']))
    <!-- Add users -->
    <div class="modal fade" id="add_users">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Add New User</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('users')}}">
                    <div class="modal-body p-4 pt-1">
                        <div class="d-flex align-items-center gap-3 flex-wrap mb-4">
                            <div class="avatar avatar-3xl border bg-light">
                                <i class="icon-images fs-28 text-dark"></i>
                            </div>
                            <div>
                                <label class="form-label">User Image<span class="text-danger"> *</span></label>
                                <p class="fs-13 mb-3">Image should be with in 5 MB</p>
                                <div class="d-flex align-items-center">
                                    <div class="btn btn-icon btn-sm btn-white rounded-circle position-relative me-2">
                                        <input type="file" class="form-control position-absolute w-100 h-100 top-0 start-0 opacity-0">
                                        <i class="icon-upload"></i>
                                    </div>
                                    <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle text-danger"><i class="icon-trash-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row algin-items-center justify-content-center">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                        <label class="form-label">First Name<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                        <label class="form-label">Last Name<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role<span class="text-danger"> *</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option>Admin / Owner</option>
                                <option>Supervisor</option>
                                <option>Cashier</option>
                                <option>Chef</option>
                                <option>Waiter</option>
                            </select>
                        </div>
                            <div class="mb-3">
                            <label class="form-label">Phone Number<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status<span class="text-danger"> *</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add users End -->

    <!-- Edit users -->
    <div class="modal fade" id="edit_users">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Edit User</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('users')}}">
                    <div class="modal-body p-4 pt-1">
                        <div class="d-flex align-items-center gap-3 flex-wrap mb-4">
                            <div class="avatar avatar-3xl border bg-light mb-3">
                                <img src="{{URL::asset('build/img/profiles/avatar-03.jpg')}}" alt="item" class="img-fluid rounded">
                            </div>
                            <div>
                                <label class="form-label">User Image<span class="text-danger"> *</span></label>
                                <p class="fs-13 mb-3">Image should be with in 5 MB</p>
                                <div class="d-flex align-items-center">
                                    <div class="btn btn-icon btn-sm btn-white rounded-circle position-relative me-2">
                                        <input type="file" class="form-control position-absolute w-100 h-100 top-0 start-0 opacity-0">
                                        <i class="icon-pencil-line"></i>
                                    </div>
                                    <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle text-danger"><i class="icon-trash-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row algin-items-center justify-content-center">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                        <label class="form-label">First Name<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" value="Emily">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                        <label class="form-label">Last Name<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" value="Johnson">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role<span class="text-danger"> *</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option>Admin / Owner</option>
                                <option selected>Supervisor</option>
                                <option>Cashier</option>
                                <option>Chef</option>
                                <option>Waiter</option>
                            </select>
                        </div>
                            <div class="mb-3">
                            <label class="form-label">Phone Number<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" value="+1 34567 89012">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status<span class="text-danger"> *</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option selected>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-1">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit users End -->

    <!-- User Permission -->
    <div class="modal fade" id="user_permission">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 p-4 pb-3">
                    <h4 class="modal-title">Permissions</h4>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="icon-x"></i></button>
                </div>
                <form action="{{url('users')}}">
                    <div class="modal-body p-4 pt-1">
                        <div class="d-flex justify-content-end mb-3">
                            <div class="form-check form-check-md">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label for="select-all">Revert All</label>
                            </div>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table m-0 table-nowrap bg-white border">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>View</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                        <th>Export</th>
                                        <th>Approved/Void</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-dark fw-medium">Dashboard</td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td class="text-dark fw-medium">POS</td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td class="text-dark fw-medium">Hold/Resume Sale</td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td class="text-dark fw-medium">Refund / Return</td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td class="text-dark fw-medium">Products</td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td class="text-dark fw-medium">Categories</td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td class="text-dark fw-medium">Customers</td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td class="text-dark fw-medium">Reports</td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td class="text-dark fw-medium">Settings</td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-md ps-0">
                                                <input class="form-check-input ms-0" type="checkbox">
                                            </div>
                                        </td>

                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-1">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Permission</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Start Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle">
                            <img src="{{URL::asset('build/img/icons/trash-icon.svg')}}" alt="trash" class="img-fluid w-auto h-auto">
                        </span>
                    </div>
                    <h4 class="mb-1">Delete Confirmation</h4>
                    <p class="mb-4">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-light w-100" data-bs-dismiss="modal">Close</a>
                        <a href="{{url('users')}}" class="btn btn-danger w-100">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->

    <!-- Start Filter -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filter-offcanvas">
        <div class="offcanvas-header pb-0">
            <div class="border-bottom d-flex align-items-center justify-content-between w-100 pb-3">
                <h4 class="offcanvas-title mb-0">Filter</h4>
                <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="offcanvas" aria-label="Close"><i class="icon-x"></i></button>
            </div>
        </div>
        <div class="offcanvas-body d-flex flex-column pt-3">
            <div>
                <div class="mb-3">
                    <label class="form-label">Name<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Name</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">John Smith
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Emily Johnson
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">David Williams
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Ashley Brown
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Michael Davis
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role<span class="text-danger"> *</span></label>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            Select
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                            <h6 class="fs-14 fw-semibold mb-3">Role</h6>
                            <div class="input-icon-end input-icon position-relative mb-3">
                                <span class="input-icon-addon">
                                    <i class="icon-search text-dark"></i>
                                </span>
                                <input type="text" class="form-control form-control-md" placeholder="Search">
                            </div>
                            <div class="vstack gap-2">
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Admin / Owner
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Supervisor
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Cashier
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Chef
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Waiter
                                    </label>
                                </div>
                                <div>
                                    <label class="d-flex align-items-center">
                                        <input class="form-check-input m-0 me-2" type="checkbox">Delivery
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="mb-3">
                    <label class="form-label">Status<span class="text-danger"> *</span></label>
                    <select class="select">
                        <option>Select</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 mt-auto border-0">
                <a href="#" class="btn btn-light w-100">Reset</a>
                <a href="#" class="btn btn-primary w-100">Apply</a>
            </div>
        </div>
    </div>
    <!-- End Filter -->
@endif
