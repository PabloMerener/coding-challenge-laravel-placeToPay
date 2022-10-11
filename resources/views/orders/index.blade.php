<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('orders.create') }}">
                {{ __('Create Order') }}
            </a>
        </h2>
    </x-slot>
    <table class="w-full whitespace-no-wrapw-full whitespace-no-wrap">
        <thead>
            <tr class="text-center font-bold">
                <td class="border px-6 py-4">Id</td>
                <td class="border px-6 py-4">Name</td>
                <td class="border px-6 py-4">Email</td>
                <td class="border px-6 py-4">Phone</td>
                <td class="border px-6 py-4">Status</td>
            </tr>
        </thead>
        @foreach($orders as $order)
        <tr>
            <td class="border px-6 py-4">
                <a href="{{ route('orders.show', $order->id) }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">
                    {{ $order->id }}
                </a>
            </td>
            <td class="border px-6 py-4">{{ $order->customer_name }}</td>
            <td class="border px-6 py-4">{{ $order->customer_email }}</td>
            <td class="border px-6 py-4">{{ $order->customer_mobile }}</td>
            <td class="border px-6 py-4">{{ $order->status }}</td>
        </tr>
        @endforeach
    </table>
    {{ $orders->links() }}
</x-app-layout>