applyFinancialYearDates() {
    const selectedId = Number(this.selectedFinancialYearId);
    const fy = this.financialYears.find((year) => Number(year.id) === selectedId);

    if (!fy) {
        return;
    }

    this.filter_start_date = fy.report_start_date || fy.start_date || '';
    this.filter_end_date = fy.report_end_date || fy.end_date || '';
},
formatFinancialYearLabel(fy) {
    let label = fy.label || '';

    if (fy.is_current) {
        label += ' ({{ __('Current') }})';
    } else if (fy.status === 'closed') {
        label += ' ({{ __('Closed') }})';
    }

    return label;
},
resetFinancialYearSelection() {
    this.selectedFinancialYearId = this.defaultFinancialYearId;
    this.applyFinancialYearDates();
},
