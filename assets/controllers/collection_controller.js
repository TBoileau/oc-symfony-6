import {Controller} from '@hotwired/stimulus';
import {Modal} from 'bootstrap';

/**
 * @class CollectionController
 */
export default class extends Controller {
  /**
   * @type {string[]}
   */
  static targets = ['items'];

  /**
   * @type {[]}
   */
  index = this.itemsTarget.childElementCount - 1;

  modal = new Modal(this.element.querySelector('#modal-item'), {});

  /**
   * Choice type of media to add
   */
  choice() {
    this.modal.show();
  }

  /**
   * Delete media
   * @param {Event} e
   */
  delete(e) {
    e.currentTarget.closest('.media').remove();
  }

  /**
   * Add media
   * @param {Event} e
   */
  add(e) {
    this.modal.hide();
    this.index++;
    const mediaElement = document.createElement('div');
    mediaElement.classList.add('media');
    mediaElement.innerHTML = this.element.dataset.prototype.replace(
        /__name__/g,
        this.index,
    );
    mediaElement.querySelector(`.${e.currentTarget.dataset.type}`)
        .classList
        .remove('d-none');
    mediaElement.querySelector(`.type`).value = e.currentTarget.dataset.type;
    this.itemsTarget.appendChild(mediaElement);
  }
}
