{% extends "templates/default.volt" %}
{% block header %}
    <script type="text/javascript">
        {#$(function download () {
            $.ajax({
                    url: "{{url('data/create')}}",
                    type: "POST",
                    data: {
                            id: {{buy.idBuy}}
                    },
                    error: function(error){
                            console.log(error);
                    },
                    success: function(data){
                            window.location = '{{url('data/download')}}/' + data[0];
                    }
            });
        });#}
    </script>
{% endblock %}
{% block content %}
    <div class="row">
        <div class="col-md-12 text-center">
            <h2>Historial de pagos</h2>
            <p></p>
        </div>
    </div>
    
    <div class="col-md-12" align="right">
	<a href="{{url(payment/downloadpdf)}}" id="download" onClick="download();" class="btn btn-info btn-sm">Descargar pagos</a>
        <p></p>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">        
                <tr>
                    <td>
                        <strong>Número de Factura:</strong>
                    </td>
                    <td>
                        <strong>Referencia:</strong>
                    </td>
                    <td>
                        <strong>Nombre del articulo:</strong>
                    </td>
                    <td>
                        <strong>Cantidad:</strong>
                    </td>
                    <td>
                        <strong>Fecha de factura:</strong>
                    </td>
                    <td>
                        <strong>Valor total:</strong>
                    </td>
                    <td>
                        <strong>Valor cancelado:</strong>
                    </td>
                    <td>
                        <strong>Saldo:</strong>
                    </td>
                </tr>
                {% for b in buys %}
                <tr>
                    <td>{{b.buy.idBuy}}</td>
                    <td>{{b.article.reference}}</td>
                    <td>{{b.article.name}}</td>
                    <td>{{b.article.quantity}}</td>
                    <td>{{b.buy.date}}</td>
                    <td>${{b.buy.value}}</td>
                    <td>${{b.buy.value - b.buy.debt}}</td>
                    <td>${{b.buy.debt}}</td>
                </tr>
                {% endfor %}
            </table>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">        
                <tr>
                    <td>
                        <strong>Número de Recibo:</strong>
                    </td>
                    <td>
                        <strong>Fecha de pago:</strong>
                    </td>
                    <td>
                        <strong>Valor:</strong>
                    </td>
                </tr>
                {% for payment in payments %}
                <tr>
                    <td>{{payment.idPayment}}</td>
                    <td>{{payment.date}}</td>
                    <td>${{payment.receiptValue}}</td>
                </tr>
                {% endfor %}
            </table>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12" align="right">
            <p>
                <em>Información sujeta a verificación.</em>
            </p>
        </div>
    </div>
{% endblock %}