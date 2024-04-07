<script src="https://cdn.tailwindcss.com"></script>

{{-- パンくず --}}
{{--
    インストール: composer install diglactic/laravel-breadcrumbs
    使い方: https://poppotennis.com/posts/laravel-breadcrumbs
    --}}
{{-- {{ Breadcrumbs::render('samplesChainCase.index') }} --}}

{{-- ページヘッダー --}}
<div class="sm:flex sm:items-center">
    {{-- タイトル --}}
    <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900">Sample 一覧</h1>
    </div>

    <div>
        <button type="button"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            onclick="location.href='{{ route('samplesChainCase.create') }}'"
        >
            新規登録
        </button>
    </div>
</div>

{{-- 検索 --}}
<div class="mt-3">
    <form method="GET" action="{{ route('samplesChainCase.index') }}">
        <div class="flex">

            <div>
                <label for="%%FIRSTCOLUMN%%" class="block text-sm font-medium leading-6 text-gray-900">%%FIRSTCOLUMN%%</label>
                <div class="mt-2">
                    <input id="%%FIRSTCOLUMN%%" type="text" name="%%FIRSTCOLUMN%%" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value="{{ request()->get('%%FIRSTCOLUMN%%') }}" />
                </div>
            </div>

            {{-- プルダウン --}}
            {{-- <div>
                <x-atoms.label for="変数" :value="__('ラベル名')" />
                <x-select id="変数" name="変数" class="mt-1 w-48" :error="$errors->has('変数')">
                    <option value="">全て</option>
                    @foreach($オプション as $key => $option)
                        <option value="{{ $option->id }}" {{ $option->id == request()->input('変数') ? 'selected' : '' }}>
                            {{ $option->id }}
                        </option>
                    @endforeach
                </x-select>
            </div> --}}

            <div class="ml-3 flex gap-1 place-items-end">
                <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">検索</button>
                <button type="button" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" onclick="location.href='{{ route('samplesChainCase.index') }}'">リセット</button>
            </div>

        </div>
    </form>
</div>

<div class="mt-5 flex flex-col">
    <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">


            {{-- ページネーション --}}
            {{-- {{ $masterJenres->links('components.pagination') }} --}}


            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg mt-2">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            %%HEADCOLUMNS%%
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($samples as $index => $sample)
                        <tr>
                            %%BODYCOLUMNS%%
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <a href="{{ route('samplesChainCase.edit', $sample->id) }}" class="text-indigo-600 hover:text-indigo-900">編集<span class="sr-only">, {{ $sample->name }}</span></a>
                            </td>
                        </tr>
                        @endforeach


                        <!-- More people... -->
                    </tbody>
                </table>


                {{-- ページネーション --}}
                {{-- <div class="p-4 border-t border-gray-200 bg-white">
                    {{ $masterJenres->links('components.pagination') }}
                </div> --}}


            </div>
        </div>
    </div>
</div>

