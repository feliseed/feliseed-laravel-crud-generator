<script src="https://cdn.tailwindcss.com"></script>

{{-- パンくず --}}
{{--
    インストール: composer install diglactic/laravel-breadcrumbs
    使い方: https://poppotennis.com/posts/laravel-breadcrumbs
    --}}
{{-- {{ Breadcrumbs::render('samplesChainCase.create') }} --}}

{{-- ページヘッダー --}}
<div class="sm:flex sm:items-center">
    {{-- タイトル --}}
    <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900">Sample 新規登録</h1>
    </div>
</div>

<div class="mt-3">
    <form class="space-y-6" action="{{ route('samplesChainCase.store') }}" method="POST">
        @csrf

        <div class="bg-white px-4 py-5 shadow sm:rounded-lg sm:p-6">
            %%COLUMNS%%
        </div>

        <div class="flex justify-end">
            <button type="button" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" onclick="location.href='{{ route('samplesChainCase.index') }}'">キャンセル</button>
            <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">保存</button>
        </div>
    </form>
</div>

