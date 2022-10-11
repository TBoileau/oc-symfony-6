import {Controller} from '@hotwired/stimulus';
import {template} from 'lodash';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/fr';

/**
 * @class TricksController
 */
export default class extends Controller {
  /**
   * @type {string[]}
   */
  static targets = ['list', 'loadMore', 'template'];

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
  updateTrickByOwnerOnly = parseInt(
      this.element.dataset.updateTrickByOwnerOnly,
  ) === 1;

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
    dayjs.extend(relativeTime).locale('fr-FR');
    this.template = template(this.templateTarget.innerHTML);
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
      trick.createdAt = dayjs(trick.createdAt).fromNow();
      trick.canBeUpdated = this.userId !== null &&
        (!this.updateTrickByOwnerOnly || this.userId === trick.user.id);
      trick.canBeDeleted = this.userId !== null &&
        (!this.deleteTrickByOwnerOnly || this.userId === trick.user.id);
      this.listTarget.innerHTML += this.template(trick);
    });

    if (!this.data._links.next) {
      this.loadMoreTarget.remove();
    }
  }
}
