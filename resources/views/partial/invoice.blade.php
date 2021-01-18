<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $customer->name }} | {{ $nama_product }}</title>  
    <style>
        .border-putus {
          border-bottom: 2px dotted black;
        }    
    </style>    
</head>
<body>
    <div id="invoice">
        <table style="width: 595px; margin: 2px;">
            <tr>
                <td style="text-align: left;"><img src="{{ asset('assets/logo.png') }}" width="35%"></img></td>
                <td style="text-align: right;vertical-align: bottom; font-size: 12px;"><strong>{{ $company->name }}</strong></td>
            </tr>
            <tr>
                <td style="font-size: 18px; border: 1px solid black; " colspan="2"><strong>FAKTUR</strong></td>
            </tr>        
            <tr>
                <td style="border-bottom: 2px solid black; font-size: 14px;" colspan="2"><br>No.</td>            
            </tr>        
        </table>
        <table  style="width: 595px; columns:3;">
            <tbody>
                <tr>
                    <td style="vertical-align: text-top; width: 20%; font-size: 12px;" class="border-putus">Nama Perusahaan</td>
                    <td style="vertical-align: text-top; width: 5px; font-size: 12px;" >:</td>
                    <td style="vertical-align: text-top; width: 400px;font-size: 12px;" class="border-putus">{{ $customer->name }}</td>
                </tr>  
                <tr>
                    <td style="vertical-align: text-top; width: 20%; font-size: 12px;" class="border-putus">Alamat</td>
                    <td style="vertical-align: text-top; width: 5px; font-size: 12px;" >:</td>
                    <td style="vertical-align: text-top; width: 400px; font-size: 12px; " class="border-putus">{{ $customer->address1 }} <br> {{ $customer->city }} {{ $customer->zipcode }}</td>
                </tr>   
                <tr>
                    <td style="vertical-align: text-top; width: 20%; font-size: 12px;" class="border-putus">NPWP</td>
                    <td style="vertical-align: text-top; width: 5px; font-size: 12px;" >:</td>
                    <td style="vertical-align: text-top; width: 400px; font-size: 12px;" class="border-putus">807739297018000</td>
                </tr>             
            </tbody>                                   
        </table>
        <table  style="width: 200px; font-size: 14px;position: relative; left: 395px; ">
            <tr>
                <td style="width: 10%;" class="border-putus"><input type="checkbox" @if($pkp == 'true') checked @endif> PKP</td> 
                <td style="width: 10%;" class="border-putus"><input type="checkbox" @if($bkn_pkp == 'true') checked @endif> Bukan PKP</td>           
            </tr>
            <tr>
                <td style="width: 15%;" class="border-putus"><input type="checkbox" @if($tunai == 'true') checked @endif> Tunai</td> 
                <td style="width: 20%;" class="border-putus"><input type="checkbox" @if($kredit == 'true') checked @endif> Kredit</td>           
            </tr>        
        </table> 
        <br>
        <table style="width: 595px; border-collapse: collapse; columns: 5;">
            <thead>
                <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: center;">Kwantum</td>
                <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;text-align: center;">Unit</td>
                <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;text-align: center;">Penjelasan</td>
                <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;text-align: center;">Harga satuan</td>
                <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;text-align: center;">Jumlah</td>
            </thead>
            <tbody>
                
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td><td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"><strong>{{ $nama_product }}</strong></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td><td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td>
                </tr>
                <!-- Head Jasa -->
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td><td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"><strong>Jasa</strong></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td><td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td>
                </tr> 
                <!-- End Head Jasa -->
                <!-- Detail Jasa -->
                @php
                    $subtotal_jasa = 0;
                @endphp
                @foreach($jasa as $value)
                    @php
                        $subtotal_jasa = $subtotal_jasa+$value->jumlah;
                    @endphp                    
                    <tr>
                        <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;">@currency($value->kwantum)</td>
                        <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;">{{ $value->satuan }}</td>
                        <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;">{{ $value->name }}</td>
                        <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;">@currency($value->component_price)</td>
                        <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;">@currency($value->jumlah)</td>
                    </tr>
                @endforeach
                <!-- End Detail Jasa -->
                <!-- Sub Total Jasa -->
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td><td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"><strong>Sub Total</strong></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;"><strong>@currency($subtotal_jasa)</strong></td>
                </tr>  
                <!-- End Sub Total Jasa -->   
                
                <!-- Head Material -->
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td><td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"><strong>Material</strong></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td><td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td>
                </tr> 
                <!-- End Head Material -->   
                <!-- Detail Material -->
                @php
                    $subtotal_mat = 0;
                @endphp
                @foreach($material as $value)
                    @php
                        $subtotal_mat = $subtotal_mat+$value->jumlah;
                    @endphp                  
                    <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;">@currency($value->kwantum)</td>
                        <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;">{{ $value->satuan }}</td>
                        <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;">{{ $value->name }}</td>
                        <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;">@currency($value->component_price)</td>
                        <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;">@currency($value->jumlah)</td>
                    </tr>
                @endforeach
                <!-- End Detail Material -->
                <!-- Sub Total Material -->
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td><td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"><strong>Sub Total</strong></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;"><strong>@currency($subtotal_mat)</strong></td>
                </tr>  
                <!-- End Sub Total Material --> 
    
                @php
                    $jumlahtotal = $subtotal_mat+$subtotal_jasa;
                @endphp                
                <tr>
                    <td rowspan="4" colspan="2" style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: center;">
                        Syarat Pembayaran :<br>
                        Mohon Pembayaran di transfer<br>
                        ke : PT. Tata Layak Prawira<br>
                        Bank Danamon - Bogor Juanda<br>
                        No. Rek. : 0104332010</td>                
                    <td colspan="2" style="border: 1px solid black; border-collapse: collapse; font-size: 12px;">Jumlah harga jual</td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;">@currency($jumlahtotal)</td>
                </tr>
                @php
                    $ppn = $jumlahtotal*$t_ppn/100;
                @endphp                 
                <tr>
                    <td colspan="2" style="border: 1px solid black; border-collapse: collapse; font-size: 12px;">PPN 10%</td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;">
                    @if($t_ppn != 0) 
                        @currency($ppn)
                    @endif
                    </td>
                </tr>
                @php
                    $materai = $t_materai;
                @endphp                 
                <tr>
                    <td colspan="2" style="border: 1px solid black; border-collapse: collapse; font-size: 12px;">Materai</td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 12px; text-align: right;">
                    @if($t_materai != 0) 
                        @currency($materai)
                    @endif                            
                    </td>
                </tr>
                @php
                    $grandtotal = $jumlahtotal+$ppn+$materai;
                @endphp                 
                <tr>
                    <td colspan="2" style="border: 1px solid black; border-collapse: collapse; font-size: 13px;"><strong>TOTAL</strong></td>
                    <td style="border: 1px solid black; border-collapse: collapse; font-size: 13px; text-align: right;"><strong>@currency($grandtotal)</strong></td>
                </tr>
                @inject('fungsi','App\Http\Controllers\Controller');
                <tr>
                    <td style="border-left: 1px solid black;border-right: 1px solid black; font-size: 13px;" colspan="5"><strong>Terbilang :</strong></td>
                </tr>            
                <tr>
                    <td style="border-left: 1px solid black;border-right: 1px solid black; border-bottom: 1px solid black; font-size: 13px; vertical-align: middle; text-align: center;" colspan="5">
                        <br><strong>{{ $fungsi->terbilang($grandtotal) }}</strong><br><br>
                    </td>
                </tr> 
                <tr>
                    <td style="font-size: 12px;" colspan="5">
                        <br>
                            Pembayaran dengan Cek dianggap sah setelah Cek tersebut dapat diuangkan (clearing)
                        <br><br>
                    </td>
                </tr> 
                <tr>
                    <td style="border: 1px solid black;font-size: 12px;" colspan="5">No. Kontrak :</td>                
                </tr> 
                <tr>
                    <td><br></td>
                </tr>  
                <tr>
                    <td colspan="5" style="text-align: center;border: 1px solid black;font-size: 12px;"><br><strong>Jatuh Tempo 14 Hari Setelah Faktur Di Terima</strong><br><br></td>
                </tr>       
            </tbody>        
        </table>
    
        <table  style="width: 200px; font-size: 14px;position: absolute; left: 395px; ">
            <tr>
                
                <td style="width: 10%; text-align: center; font-size: 12px;"><br>Jakarta, 11 Januari 2021</td>                      
            </tr>
            <tr>            
                <td style="width: 15%; text-align: center; font-size: 12px;"><br><br><br><strong><u>Bag. Admin/Bag. Keu</u></strong></td>                       
            </tr>        
        </table>  
        
        <table  style="width: 200px; font-size: 14px;position: relative; left: 12px; ">
            <tr>                
                <td><img src="https://chart.googleapis.com/chart?cht=qr&chl=Hello+World&chs=80x80&chld=L|0"/></td>                      
            </tr>      
        </table>         
        
        <br>
        <table style="width: 595px; ">
            <tr>
                <td style="font-size: 12px; text-align: center; border-bottom: 20px solid grey;">{{ $company->alamat }} {{ $company->kota }} {{ $company->kdpos }}</td>                
            </tr>            
        </table>
    </div>
    <button onclick="printContent('invoice')">Print</button>
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