function rackApp() {
    return {
        category: "pieces",
        selected: null,

        selectRack(code) {
            this.selected = code;
            console.log("Rak terpilih:", code);
        },
    };
}
