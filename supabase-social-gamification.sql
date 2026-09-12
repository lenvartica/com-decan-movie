-- DECAN MOVIE optional social/gamification upgrade.
-- Run after your existing supabase-schema.sql. Does not modify player/streaming links.

create table if not exists public.movie_reviews (
  id uuid primary key default gen_random_uuid(),
  user_id uuid not null references public.profiles(id) on delete cascade,
  movie_id bigint not null,
  media_type text not null check (media_type in ('movie','tv')),
  body text not null check (char_length(trim(body)) between 1 and 4000),
  spoiler boolean not null default false,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create table if not exists public.review_likes (
  review_id uuid not null references public.movie_reviews(id) on delete cascade,
  user_id uuid not null references public.profiles(id) on delete cascade,
  created_at timestamptz not null default now(),
  primary key(review_id,user_id)
);

create table if not exists public.user_follows (
  follower_id uuid not null references public.profiles(id) on delete cascade,
  following_id uuid not null references public.profiles(id) on delete cascade,
  created_at timestamptz not null default now(),
  primary key(follower_id,following_id),
  check(follower_id <> following_id)
);

create table if not exists public.achievements (
  id text primary key,
  name text not null,
  description text not null,
  xp integer not null default 0
);

create table if not exists public.user_achievements (
  user_id uuid not null references public.profiles(id) on delete cascade,
  achievement_id text not null references public.achievements(id) on delete cascade,
  earned_at timestamptz not null default now(),
  primary key(user_id,achievement_id)
);

alter table public.movie_reviews enable row level security;
alter table public.review_likes enable row level security;
alter table public.user_follows enable row level security;
alter table public.achievements enable row level security;
alter table public.user_achievements enable row level security;

drop policy if exists "reviews public read" on public.movie_reviews;
create policy "reviews public read" on public.movie_reviews for select using (true);
drop policy if exists "reviews own insert" on public.movie_reviews;
create policy "reviews own insert" on public.movie_reviews for insert to authenticated with check (auth.uid()=user_id);
drop policy if exists "reviews own update" on public.movie_reviews;
create policy "reviews own update" on public.movie_reviews for update to authenticated using (auth.uid()=user_id) with check (auth.uid()=user_id);
drop policy if exists "reviews own delete" on public.movie_reviews;
create policy "reviews own delete" on public.movie_reviews for delete to authenticated using (auth.uid()=user_id);

drop policy if exists "review likes public read" on public.review_likes;
create policy "review likes public read" on public.review_likes for select using (true);
drop policy if exists "review likes own" on public.review_likes;
create policy "review likes own" on public.review_likes for all to authenticated using (auth.uid()=user_id) with check (auth.uid()=user_id);

drop policy if exists "follows public read" on public.user_follows;
create policy "follows public read" on public.user_follows for select using (true);
drop policy if exists "follows own" on public.user_follows;
create policy "follows own" on public.user_follows for all to authenticated using (auth.uid()=follower_id) with check (auth.uid()=follower_id);

drop policy if exists "achievements public read" on public.achievements;
create policy "achievements public read" on public.achievements for select using (true);
drop policy if exists "user achievements public read" on public.user_achievements;
create policy "user achievements public read" on public.user_achievements for select using (true);

insert into public.achievements(id,name,description,xp) values
('first_review','First Review','Publish your first movie review.',25),
('five_movies','Movie Explorer','Rate or watch five titles.',50),
('ten_day_streak','10 Day Streak','Maintain a ten-day activity streak.',100)
on conflict (id) do nothing;

create index if not exists movie_reviews_movie_idx on public.movie_reviews(movie_id,media_type,created_at desc);
create index if not exists review_likes_review_idx on public.review_likes(review_id);
create index if not exists follows_following_idx on public.user_follows(following_id);
