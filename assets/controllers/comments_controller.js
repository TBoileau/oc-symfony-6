import {Controller} from '@hotwired/stimulus';

/**
 * @class CommentsController
 */
export default class extends Controller {
  /**
   * @type {string[]}
   */
  static targets = ['list', 'loadMore'];

  /**
   * @var {
   *      {
   *          page: int,
   *          pages: int,
   *          limit: int,
   *          total: int,
   *          count: int,
   *          _links: {
   *            self: {href: string},
   *            next?: {href: string}
   *          },
   *          _embedded: {
   *              comments: {
   *                createdAt: string,
   *                content: string,
   *                user: {nickname: string}
   *              }[]
   *          }
   *      }
   *  }
   */
  data = {
    _links: {
      next: {
        href: `/api/tricks/${this.element.dataset.trickId}/comments`,
      },
    },
  };

  /**
   * Connect
   */
  connect() {
    this.next();
  }

  /**
   * Next page
   */
  next() {
    if (this.data._links.next) {
      fetch(this.data._links.next.href)
          .then((res) => res.json())
          .then((data) => this.data = data)
          .then(this.load.bind(this));
    }
  }

  /**
   * Load page
   */
  load() {
    this.data._embedded.comments.forEach((comment) => {
      const commentElement = document.createElement('div');
      commentElement.innerHTML = comment.content;
      this.listTarget.appendChild(commentElement);
    });

    if (!this.data._links.next) {
      this.loadMoreTarget.remove();
    }
  }
}
