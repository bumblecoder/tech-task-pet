import { format } from 'date-fns';
import { enUS } from 'date-fns/locale';

document.addEventListener('DOMContentLoaded', function () {
    const apiEndpoint = '/api/articles';
    let currentPage = 1;
    const perPage = 5;

    async function fetchArticles(page = 1) {
        try {
            const response = await fetch(`${apiEndpoint}?page=${page}&per_page=${perPage}`);
            if (!response.ok) {
                throw new Error('Network error');
            }
            const data = await response.json();
            const totalCount = parseInt(response.headers.get('X-Total-Count'), 10) || 0;
            return { articles: data, totalCount };
        } catch (error) {
            console.error('Error during the loading...', error);
            return { articles: [], totalCount: 0 };
        }
    }

    function renderArticles(articles) {
        const articlesTable = document.querySelector('#articles tbody');
        if (!articlesTable) {
            console.error('Table body not found');
            return;
        }
        articlesTable.innerHTML = '';

        if (!Array.isArray(articles) || articles.length === 0) {
            articlesTable.innerHTML = '<tr><td colspan="4">There are no available articles...</td></tr>';
            return;
        }

        articles.forEach(article => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${article.id}</td>
                <td>${article.title}</td>
                <td>${article.content}</td>
                <td>${article.created_at ? format(new Date(article.created_at), 'MMMM dd\'th\' hh:mm a', { locale: enUS }) : 'N/A'}</td>
                <td><img src="${article.image}" alt="Article Image" class="rounded-image"></td>
            `;
            articlesTable.appendChild(tr);
        });
    }


    function renderPagination(totalCount) {
        const paginationContainer = document.getElementById('pagination');
        if (!paginationContainer) {
            console.error('Pagination container not found');
            return;
        }
        const totalPages = Math.ceil(totalCount / perPage);

        const prevButton = document.getElementById('prev');
        const nextButton = document.getElementById('next');
        const pageInfo = document.getElementById('page-info');

        if (!prevButton || !nextButton || !pageInfo) {
            console.error('Pagination elements not found');
            return;
        }

        prevButton.disabled = currentPage === 1;
        nextButton.disabled = currentPage === totalPages;
        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

        prevButton.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                loadPage(currentPage);
            }
        };

        nextButton.onclick = () => {
            if (currentPage < totalPages) {
                currentPage++;
                loadPage(currentPage);
            }
        };
    }

    async function loadPage(page) {
        const { articles, totalCount } = await fetchArticles(page);
        renderArticles(articles);
        renderPagination(totalCount);
    }

    loadPage(currentPage);
});
