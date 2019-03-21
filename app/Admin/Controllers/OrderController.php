<?php

namespace App\Admin\Controllers;

use App\Models\Good;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

class OrderController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('订单管理')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $startT = date('Ymd').'0';
       // dd($startT);  日期设置错误
        $grid = new Grid(new Order);
        if(Admin::user()->isRole('UserOrderJd')){
            $grid->model()->where('good_id','1')->where('start_time',$startT)->orderBy('start_time', 'asc');

        }elseif(Admin::user()->isRole('UserOrderKe')){
            $grid->model()->where('good_id','2')->orwhere('good_id','3')->where('start_time',$startT)->orderBy('start_time', 'asc');

        }elseif(Admin::user()->isRole('UserOrderKs')){
            $grid->model()->where('good_id','4')->where('start_time',201902200)->orderBy('start_time', 'asc');

        }

        $grid->payment_no('订单号');
        $grid->user_id('姓名')->display(function($user_id) {
            return User::find($user_id)->name;
        });
        $grid->director('手机号码')->display(function() {
            return User::find($this->user_id)->phone;
        });
        $grid->column('good_id','类别')->display(function($good_id) {
            return Good::findOrFail($good_id)->good_name;
        });
        if(Admin::user()->isRole('UserOrderKe')) {
            $grid->colum('车辆品牌')->display(function () {
                if ($this->good_id == 2) {
                    return '福特';
                } else {
                    return '雪铁龙';
                }
            });
        }elseif(Admin::user()->isRole('UserOrderKs')){
            return '雪铁龙';
        }
        //$grid->total_price('订单金额');
        $grid->column('start_time','开始时间')->display(function ($start_time) {
            if(strlen($start_time) == 9){
                return $start_time[2].$start_time[3].'年'.$start_time[4].$start_time[5].'月'.$start_time[6].$start_time[7].'日';
            }else{
                return $start_time[2].$start_time[3].'年'.$start_time[4].$start_time[5].'月'.$start_time[6].$start_time[7].'日'.$start_time[8].$start_time[9].':00';
            }

        });

        $grid->column('end_time','结束时间')->display(function ($end_time) {
            if(strlen($end_time) == 9){
                return $end_time[2].$end_time[3].'年'.$end_time[4].$end_time[5].'月'.$end_time[6].$end_time[7].'日';
            }else{
                return $end_time[2].$end_time[3].'年'.$end_time[4].$end_time[5].'月'.$end_time[6].$end_time[7].'日'.$end_time[8].$end_time[9].':00';
            }

        });
        $grid->plate('车牌号码');
        $states = [
            'on'  => ['value' => 1, 'text' => '已使用', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '未使用', 'color' => 'default'],
        ];
        $grid->closed('是否已使用')->switch($states);
        $grid->filter(function($filter){
            $filter->disableIdFilter();
        });
        $grid->disableCreateButton();
        $grid->disableRowSelector();
     //   $grid->disableActions();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
//            $actions->disableEdit();
            $actions->disableView();

        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

        $show->id('Id');
//        $show->user_id('User id');
        $show->good_id('Good id');
        $show->order_status('Order status');
        $show->total_price('Total price');
        $show->start_time('Start time');
        $show->end_time('End time');
//        $show->created_at('Created at');
//        $show->updated_at('Updated at');
        $show->paid_at('Paid at');
        $show->payment_no('Payment no');
//        $show->refund_status('Refund status');
//        $show->refund_no('Refund no');
        $show->closed('是否已用');
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

//        $form->number('user_id', 'User id');
//        $form->number('good_id', 'Good id');
//        $form->number('order_status', 'Order status');
//        $form->number('total_price', 'Total price');
//        $form->text('start_time', 'Start time');
//        $form->text('end_time', 'End time');
//        $form->datetime('paid_at', 'Paid at')->default(date('Y-m-d H:i:s'));
//        $form->text('payment_no', 'Payment no');
        $form->text('plate','车牌号码')->placeholder('请输入顾客使用的车辆车牌');
//        $form->text('refund_status', 'Refund status');
//        $form->text('refund_no', 'Refund no');
        $states = [
            'on'  => ['value' => 1, 'text' => '已使用', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '未使用', 'color' => 'default'],
        ];
        $form->switch('closed','是否已使用')->states($states);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();
        });
        $form->footer(function ($footer) {
            $footer->disableReset();
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });
        return $form;
    }
}
