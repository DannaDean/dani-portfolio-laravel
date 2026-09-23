export interface Project {
    id: number;
    title: string;
    link: string | null;
    categories: string[] | string | null;
    desk_img: string | null;
    mobile_img: string | null;
}

export interface Skill {
    id: number;
    title: string | null;
    image: string | null;
}

export interface Fact {
    id: number;
    title: string;
    text: string | null;
}
