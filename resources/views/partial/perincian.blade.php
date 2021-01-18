<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perincian</title>  
    <style>
        .border-putus {
          border-bottom: 2px dotted black;
        }  

        table,th,td {
            border: 1px solid black;            
            border-collapse: collapse;            
        }   
        .margin-btn-print{
            margin-top: 10pt;
        }
    </style>    
</head>
<body>
    <div id="perincian">
        <img src="{{ asset('assets/logo.png') }}" width="10%"></img>
        <p style="text-align: left;vertical-align: bottom; font-size: 12px;"><strong>{{ $company->name }}</strong></p>
        <p style="font-size: 14px;" colspan="2"><br>PERINCIAN PENCETAKAN <br>PERIODE JANUARI 2021</p> 

        <table>            
            <thead>
                <td>No</td>
                <td>Kd_app</td>
                <td>Cycle</td>
                <td>Pengerjaan</td>
                <!-- looping berapa banyak component yang dipakai ? -->
                @foreach($components_project as $value)
                    <td>{{ $value->name }}</td>
                @endforeach
                <!-- batas looping berapa banyak component yang dipakai ? -->
            </thead> 
            
            @inject('comp_out','App\Http\Controllers\Adm\GenController')
            
            <tbody>
                @foreach($production_data as $value)
                    <tr>
                        <td>{{ ($production_data->perPage()*($production_data->currentPage()-1)) +$loop->iteration }}</td>
                        <td>{{ $value->name }}</td>
                        <td>{{ $value->cycle }}/{{ $value->jenis }}/{{ $value->part }}</td>
                        <td>{{ $value->jml }}</td>
                        <!-- looping berapa banyak percomponent yang dipakai ? -->
                        @php 
                            $components = $comp_out->getComponentsByticket($value->job_ticket);
                        @endphp

                        @foreach($components as $val)
                            <td>{{ $val->qty }}</td>
                        @endforeach
                        <!-- looping berapa banyak percomponent yang dipakai ? -->
                    </tr>
                @endforeach
            </tbody>   
            <tfoot>
                <td colspan="3" >Jumlah</td>
                <!-- looping total percomponent yang dipakai ? -->

                @foreach($total_perinci as $valu)
                    <td>{{ $valu->jml }}</td>
                @endforeach
                <!-- looping total percomponent yang dipakai ? -->
            </tfoot>    
        </table>
    </div>

    <div class="margin-btn-print">
        <button onclick="printContent('perincian')">Print</button>
    </div>
    
</body>

</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" language="javascript">

    function printContent(el){
		var restorepage = document.body.innerHTML;
		var printcontent = document.getElementById(el).innerHTML;
		document.body.innerHTML = printcontent;
		window.print();
		document.body.innerHTML = restorepage;
	}

    $(function() {
        $(this).bind("contextmenu", function(e) {
            e.preventDefault();
        });
        $(this).bind("selectstart", function(e) {
            e.preventDefault();
        });     
        $(this).bind("dragstart", function(e) {
            e.preventDefault();
        });            
    }); 
</script>