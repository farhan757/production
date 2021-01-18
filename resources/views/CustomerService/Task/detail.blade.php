                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Request : {{ $data->title }}</h4>
                          </div>
                          <div class="modal-body">
                        <h2>Request : {{ $data->title }}</h2>
                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                        Message : {{ $data->desc }}
                        </p>
                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                            @if(count($files)>0)
                                File tersedia
                                @foreach($files as $value)
                                    {{ $value }}<br>
                                @endforeach
                            @else
                                File tidak tersedia
                            @endif
                        @if(count($files)>0)
                        <a href="task/download/{{ $data->id }}" class="btn btn-success">Download</a>
                        @endif
                        </p>

    <ul class="timeline">

    <!-- /.timeline-label -->
    @foreach($list as $key=>$value)
    <!-- timeline item -->
    <li>
        <!-- timeline icon -->
        <i class="fa {{ $value->icon }} bg-blue"></i>
        <div class="timeline-item">
            <span class="time"><i class="fa fa-clock-o"></i> {{ $value->update_dt }}</span>

            <h3 class="timeline-header"><a href="#">{{ $value->username }}</a> {{ $value->name }}</h3>

            <div class="timeline-body">
                {{ $value->ket }}
            </div>
        </div>
    </li>
    <!-- END timeline item -->
    @endforeach
    
            <li>
              <i class="fa fa-clock-o bg-gray"></i>
            </li>

</ul>

                          </div>
                          <div class="modal-footer">
                            @if($issama==1)
                            <button class="btn btn-info pull-right">Complete</button>
                            @endif
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                          </div>

