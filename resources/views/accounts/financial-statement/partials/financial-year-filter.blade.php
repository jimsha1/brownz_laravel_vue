@php($columnClass = $columnClass ?? 'col-lg-3 col-md-4 col-sm-6 mb-3')

<div class="form-group {{ $columnClass }}">
    <label for="financial_year_id">{{ __('Financial Year') }}</label>
    <div class="search-box-bar-container jus-heigh-search-b">
        <select id="financial_year_id"
            class="form-control brawnz-search-bar-in"
            v-model="selectedFinancialYearId"
            @change="onFinancialYearChange">
            <option v-for="fy in financialYears" :key="fy.id" :value="fy.id">
                @{{ formatFinancialYearLabel(fy) }}
            </option>
        </select>
    </div>
</div>
