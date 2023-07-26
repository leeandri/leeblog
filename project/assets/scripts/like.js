import axios from "axios";

export default class Like {
  constructor(likeElements) {
    this.likeElements = likeElements;

    if (this.likeElements) {
      this.init();
    }
  }

  init() {
    this.likeElements.map((element) => {
      element.addEventListener("click", this.onClick);
    });
  }

  onClick(event) {
    event.preventDefault();
    const url = this.href;

    axios.get(url).then((res) => {
      console.log(res, this);
      const nb = res.data.nbLike;
      const span = this.querySelector("span");

      this.dataset.nb = nb;
      span.innerHTML = nb + " Like";

      const thumbUpFilled = this.querySelector("svg.filled");
      const thumbUpUnFilled = this.querySelector("svg.unfilled");

      thumbUpFilled.classList.toggle("hidden");
      thumbUpUnFilled.classList.toggle("hidden");
    });
  }
}
