export const BLOG_POST_LIST = 'BLOG_POST_LIST';
export const BLOG_POST_LIST_ADD = 'BLOG_POST_LIST_ADD';

export const blogPostList = () => ({
    type: BLOG_POST_LIST,
    data: [
        {
            id: 1,
            title: 'Post 1'
        },
        {
            id: 2,
            title: 'Post 2'
        },
        {
            id: 3,
            title: 'Post 3'
        }
    ]
});

export const blogPostListAdd = () => ({
    type: BLOG_POST_LIST_ADD,
    data: {
        id: Math.floor(Math.random() * 100 + 3),
        title: 'A newly added blgo post'
    }
});