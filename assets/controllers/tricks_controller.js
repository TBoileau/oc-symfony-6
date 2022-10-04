import {Controller} from '@hotwired/stimulus';

/**
 * @class TricksController
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
   *              tricks: {
   *                name: string,
   *                slug: string,
   *                category: {name: string},
   *                user: {nickname: string},
   *                cover: {filename}
   *              }[]
   *          }
   *      }
   *  }
   */
  data = {_links: {next: {href: '/api/tricks'}}};

  /**
   * @type {boolean}
   */
  deleteTrickByOwnerOnly = parseInt(
      this.element.dataset.deleteTrickByOwnerOnly,
  ) === 1;

  /**
   * @type {number|null}
   */
  userId = this.element.dataset.userId !== null ?
    parseInt(this.element.dataset.userId) :
    null;

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
    this.data._embedded.tricks.forEach((trick) => {
      const trickElement = document.createElement('div');

      const showElement = document.createElement('a');
      showElement.innerHTML = trick.name;
      showElement.href = `/${trick.slug}`;
      trickElement.appendChild(showElement);

      if (
        this.userId !== null &&
        (!this.deleteTrickByOwnerOnly || this.userId === trick.user.id)
      ) {
        const deleteElement = document.createElement('a');
        deleteElement.innerHTML = 'Supprimer';
        deleteElement.href = `/${trick.slug}/delete`;
        trickElement.appendChild(deleteElement);
      }

      this.listTarget.appendChild(trickElement);
    });

    if (!this.data._links.next) {
      this.loadMoreTarget.remove();
    }
  }
}
