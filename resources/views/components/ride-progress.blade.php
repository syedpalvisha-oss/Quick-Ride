@props(['ride'])

@php
    $stages = [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'arrived' => 'Arrived',
        'completed' => 'Completed'
    ];
    $stageLevels = ['pending' => 1, 'accepted' => 2, 'arrived' => 3, 'completed' => 4];
    
    // If it's a scheduled ride that hasn't started, it might be 'scheduled', which we treat as pre-pending (0)
    $currentLevel = $stageLevels[$ride->status] ?? 0;
    if ($ride->status === 'cancelled') {
        $currentLevel = 0; // Or handle cancel state specially
    }
@endphp

<div class="flex items-center justify-between text-xs font-semibold text-gray-500 mt-6 mb-2 max-w-lg w-full mx-auto">
    @foreach($stages as $key => $label)
        <div class="flex flex-col items-center flex-1 relative">
            <div class="w-3 h-3 rounded-full mb-1.5 z-10 transition-all duration-500
                @if($ride->status === 'cancelled') 
                    bg-gray-700
                @elseif($stageLevels[$key] < $currentLevel) 
                    bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]
                @elseif($stageLevels[$key] === $currentLevel) 
                    bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.8)] scale-125
                @else 
                    bg-gray-700
                @endif">
            </div>
            
            <span class="transition-colors duration-500 @if($stageLevels[$key] <= $currentLevel && $ride->status !== 'cancelled') text-gray-200 @else text-gray-600 @endif">{{ $label }}</span>
            
            @if(!$loop->last)
            <div class="absolute top-[5px] left-[50%] right-[-50%] h-[2px] -z-0 transition-all duration-500
                @if($ride->status === 'cancelled') 
                    bg-gray-700
                @elseif($stageLevels[$key] < $currentLevel) 
                    bg-green-500 
                @else 
                    bg-gray-700 
                @endif">
            </div>
            @endif
        </div>
    @endforeach
</div>
