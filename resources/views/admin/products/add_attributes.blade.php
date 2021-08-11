@extends('layouts.admin_layout.admin_layout')
@section('content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Products</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Product Attributes</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if ($errors->any())
                    <div class="alert alert-danger" style="margin-top: 10px">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ Session::get('success_message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form name="productForm" id="productForm"
                      @if(empty($productdata['id']))
                      action="{{ url('admin/add-edit-product') }}"
                      @else
                      action="{{ url('admin/add-edit-product/'.$productdata['id']) }}"
                      @endif
                      method="post" enctype="multipart/form-data">@csrf
                <!-- SELECT2 EXAMPLE -->
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">{{ $title }}</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label for="product_name">Product Name : </label> {{ $productdata['product_name'] }}
                                    </div>

                                    <div class="form-group">
                                        <label for="product_code">Product Code : </label> {{ $productdata['product_code'] }}
                                    </div>

                                    <div class="form-group">
                                        <label for="product_color">Product Color : </label> {{ $productdata['product_color'] }}
                                    </div>


                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <img style="width:150px;margin-top:5px;" src="{{ asset('images/product_images/small/'.$productdata['main_image']) }}">
                                        &nbsp;
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="field_wrapper">
                                        <div>
                                            <input type="text" name="field_name[]" value=""/>
                                            <a href="javascript:void(0);" class="add_button" title="Add field">&nbsp;Add</a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button  type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>

                </form>
            </div>

        </section>

    </div>

@endsection
