let membersList = {
    rows: null,
    perPage: 20,
    paginationButtons: null,
    lastPage: null,
    currentPage: 1,

    init: function () {
        // Set up image popups
        $('a.popup-img').magnificPopup(defaultMagnificConfig);

        // Setup filtering and pagination
        this.rows = $('#members-table').find('tr');
        this.setupFiltering();
        this.setupPagination();
        this.showPage(this.currentPage);
    },

    setupFiltering: function () {
        // Make all rows initially visible
        this.rows.attr('data-matches-search', 1);

        // Apply filter upon any character being entered
        let searchInput = $('#filter-members');
        searchInput.bind('change paste keyup', function () {
            let matching = $(this).val();
            membersList.filter(matching);
        });
    },

    /**
     * Returns the number of rows that can be viewed
     * (not filtered out by category or search term)
     */
    countViewableRows: function () {
        return this.rows
            .filter('[data-matches-search=1]')
            .length;
    },

    setupPagination: function () {
        this.lastPage = Math.ceil(this.countViewableRows() / this.perPage);

        $('.pagination-loading').remove();

        // Generate pagination buttons
        let hasButton;
        let prevButton = $('.members-pagination button:last-child');
        let onClick = function (event) {
            event.preventDefault();
            let page = $(this).data('page-num');
            membersList.showPage(page);
        };
        for (let page = 1; page <= this.lastPage; page++) {
            // Take no action if button already exists
            hasButton = $('.members-pagination button[data-page-num=' + page +']').length > 0;
            if (hasButton) {
                continue;
            }

            // Generate new button
            $('<button></button>')
                .html(page)
                .addClass('btn btn-default')
                .attr('data-page-num', page)
                .click(onClick)
                .insertBefore(prevButton);
        }

        // Remove extraneous pagination buttons
        let outOfBoundsPage = this.lastPage + 1;
        let outOfBoundsButton;
        while (true) {
            const selector = '.members-pagination button[data-page-num=' + outOfBoundsPage + ']';
            outOfBoundsButton = $(selector);
            if (outOfBoundsButton.length > 0) {
                outOfBoundsButton.remove();
                outOfBoundsPage++;
                continue;
            }

            break;
        }

        this.paginationButtons = $('.members-pagination button');

        // Set up previous and next buttons
        this.paginationButtons.filter(':first-child').click(function (event) {
            event.preventDefault();
            membersList.showPage(membersList.currentPage - 1);
        });
        this.paginationButtons.filter(':last-child').click(function (event) {
            event.preventDefault();
            membersList.showPage(membersList.currentPage + 1);
        });
    },

    showPage: function (pageNum) {
        pageNum = Math.min(pageNum, this.lastPage);
        pageNum = Math.max(pageNum, 1);
        this.currentPage = pageNum;

        // Show only appropriate rows
        let rowsToSkip = (pageNum - 1) * this.perPage;
        let rowsToShow = this.perPage;
        this.rows.each(function () {
            let row = $(this);
            if (row.attr('data-matches-category') === '0' || row.attr('data-matches-search') === '0') {
                row.hide();
                return;
            }

            if (rowsToSkip > 0) {
                rowsToSkip--;
                row.hide();
                return;
            }

            if (rowsToShow > 0) {
                rowsToShow--;
                row.show();
                return;
            }

            row.hide();
        });

        // Add 'active' class to appropriate button
        this.paginationButtons.removeClass('active');
        this.paginationButtons.filter('[data-page-num=' + pageNum + ']').addClass('active');

        // Enable/disable previous and next buttons
        const onFirstPage = parseInt(this.currentPage) === 1;
        const onLastPage = parseInt(this.currentPage) === parseInt(this.lastPage);
        this.paginationButtons.filter(':first-child').prop('disabled', onFirstPage);
        this.paginationButtons.filter(':last-child').prop('disabled', onLastPage);
    },

    filter: function (searchTerm) {
        if (searchTerm === '') {
            this.rows.attr('data-matches-search', 1);
        } else {
            this.rows.each(function () {
                let row = $(this);
                const memberName = row.data('member-name');
                searchTerm = searchTerm.toLowerCase();
                const matches = memberName.search(searchTerm) === -1 ? 0 : 1;
                row.attr('data-matches-search', matches);
            });
        }
        this.setupPagination();
        this.showPage(1);
    }
};
