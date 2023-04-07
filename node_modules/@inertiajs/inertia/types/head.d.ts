export default function createHeadManager(isServer: boolean, titleCallback: ((title: string) => string), onUpdate: ((elements: string[]) => void)): ({
    createProvider: () => ({
        update: (elements: Array<string>) => void;
        disconnect: () => void;
    });
});
