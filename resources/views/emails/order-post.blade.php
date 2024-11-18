<p>你单号为: {{ $order->order_no }} 的订单已经发货</p>

<h4>其中包含的商品为: </h4>

<ul>
    @foreach($order->orderDetail()->with('goods')->get() as $detail)
        <li>{{ $detail->goods->goods_name }}, 单价为: {{ $detail->price }}, 数量为: {{ $detail->number }}</li>
    @endforeach
</ul>

<h5>总付款: {{ $order->amount }}</h5>
