<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Warehouse <small>v1</small></div>
                    <h2><a id="Como_Utilizar_2"></a>Como Utilizar:</h2>
                    <ul>
                        <li>Esta API necessita de uma chave de autenticação para poder ser utilizada. Basta informar ela no header ‘auth’</li>
                    </ul>
                    <h3><a id="to_send_data_6"></a>to send data:</h3>
                    <blockquote>
                        <p>via POST:</p>
                    </blockquote>
                <pre><code>api.warehouse.io<span class="hljs-regexp">/v1/log</span></code></pre>
                    <p>with json containing the following structure exemplified:</p><pre><code class="language-javascript">{
                <span class="hljs-string">"identifier"</span>: <span class="hljs-string">"34234"</span>,
                <span class="hljs-string">"log_name"</span>: <span class="hljs-string">"cnova_stock_update"</span>,
                <span class="hljs-string">"level"</span>: <span class="hljs-string">"critical"</span>,
                <span class="hljs-string">"content"</span>: <span class="hljs-string">"Procuct sku: 34234 can not be updated"</span>,
                <span class="hljs-string">"site"</span>: <span class="hljs-string">"amazon-uk"</span>
}
        </code>
        </pre>
                    <p>you must send the exact data, or this will not work.
                        level must be one of this: ‘critical’, ‘warning’, ‘info’. anything else has to be changed on code and db scheme.</p>
                    <p>This is how we expect the data format (every field is required)</p>
<pre><code class="language-javascript">{
        <span class="hljs-string">"identifier"</span>: <span class="hljs-string">"string|integer"</span>,
        <span class="hljs-string">"log_name"</span>: <span class="hljs-string">"string"</span>,
        <span class="hljs-string">"level"</span>: <span class="hljs-string">"string"</span>,
        <span class="hljs-string">"content"</span>: <span class="hljs-string">"string"</span>,
        <span class="hljs-string">"site"</span>: <span class="hljs-string">"string"</span>
        }
    </code></pre>
                    <h3><a id="to_get_data_36"></a>to get data:</h3>
                    <blockquote>
                        <p>via GET
                            you have only one resource</p>
                    </blockquote>
                    <h4><a id="get_by_site_name_40"></a>get by site name</h4>
                    <blockquote>
                        <p>/site/{site_here}</p>
                    </blockquote>
<pre><code><span class="hljs-string">ex:</span>
        api.warehouse.io<span class="hljs-regexp">/v1</span><span class="hljs-regexp">/site/</span>amazon-uk
    </code></pre>
                    <blockquote>
                        <blockquote></blockquote>
                    </blockquote>
                    <h4><a id="get_with_filters_48"></a>get with filters</h4>
                    <blockquote>
                        <p>Voce pode utilizar FILTROS para que possa pegar informações mais precisas</p>
                    </blockquote>
                    <ul>
                        <li>
                            <p>Lista de Filtros</p>
                            <ul>
                                <li>log_name  = [string]</li>
                                <li>identifier = [string | integer]</li>
                                <li>level = [string(info, warning, critical)]</li>
                                <li>limit = [integer]</li>
                                <li>order = [string(desc, asc)]</li>
                            </ul>
                        </li>
                        <li>
                            <p>Por padrão, se LIMIT não é definido, é retornado apenas os 25 ultimos registros enviados</p>
                        </li>
                        <li>
                            <p>o filtro LIMIT irá retornar no máximo os 100 ultimos registros</p>
                        </li>
                        <li>
                            <p>Por padrão, a ordem de retorno do metodo GET /site é DESC. ou seja, irá retornar dos registros mais recentes pros mais antigos</p>
                        </li>
                    </ul>
                    <p>Some examples:</p>
                    <h5><a id="getting_all_amazonuk_logs_with_critical_level_63"></a>getting all amazon-uk logs with critical level</h5>
<pre><code>api.warehouse.io<span class="hljs-regexp">/v1</span><span class="hljs-regexp">/site/</span>amazon-uk?level=critical&amp;identifier=abc123&amp;order=ASC
    </code></pre>
                    <p>or</p>
                    <h5><a id="getting_only_3_amazonuk_logs_with_critical_level_68"></a>getting only 3 amazon-uk logs with critical level</h5>
<pre><code>api.warehouse.io/v1/site/amazon-uk?level=critical&amp;<span class="hljs-built_in">log</span>_name=product_update&amp;<span class="hljs-built_in">limit</span>=<span class="hljs-number">3</span>
    </code></pre>
                    <h1><a id="_74"></a></h1>
                    <blockquote>
                        <h5><a id="Support_to_MongoDb_in_development_75"></a>Support to MongoDb in development</h5>
                    </blockquote>
                </div>
            </div>
        </div>
    </body>
</html>
